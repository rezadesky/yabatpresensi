<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Institution;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // Helper: Mendapatkan data pegawai dari user yang sedang login
    protected function getAuthenticatedEmployee(Request $request)
    {
        $user = auth()->user();

        // 1. Jika yang login adalah user pegawai yang terhubung ke model Employee
        if ($user && $user->employee) {
            return $user->employee;
        }

        // 2. Jika user memiliki email yang cocok dengan Employee
        if ($user) {
            $emp = Employee::where('email', $user->email)->first();
            if ($emp) {
                return $emp;
            }
        }

        // 3. Fallback: Jika admin membuka mobile portal, ambil pegawai pertama sebagai tinjauan
        return Employee::with('institution')->first();
    }

    // 1. Mobile Tab Beranda (Ringkasan Kehadiran & Jadwal Kerja)
    public function mobileBeranda(Request $request)
    {
        $selectedEmployee = $this->getAuthenticatedEmployee($request);

        $today = Carbon::today()->toDateString();
        $todayAttendance = null;
        $workSchedule = null;

        if ($selectedEmployee) {
            $todayAttendance = Attendance::where('employee_id', $selectedEmployee->id)
                ->whereDate('date', $today)
                ->first();

            $workSchedule = \App\Models\WorkSchedule::where('institution_id', $selectedEmployee->institution_id)
                ->where('is_active', true)
                ->first();
        }

        return view('mobile_beranda', compact(
            'selectedEmployee',
            'todayAttendance',
            'workSchedule'
        ));
    }

    // 2. Mobile Tab Presensi GPS (Check In / Check Out)
    public function mobile(Request $request)
    {
        $selectedEmployee = $this->getAuthenticatedEmployee($request);

        $today = Carbon::today()->toDateString();
        $todayAttendance = null;

        if ($selectedEmployee) {
            $todayAttendance = Attendance::where('employee_id', $selectedEmployee->id)
                ->whereDate('date', $today)
                ->first();
        }

        return view('mobile_presensi', compact(
            'selectedEmployee',
            'todayAttendance'
        ));
    }

    // 3. Mobile Tab Riwayat Presensi (Dengan Filter Periode)
    public function mobileRiwayat(Request $request)
    {
        $selectedEmployee = $this->getAuthenticatedEmployee($request);
        $period = $request->get('period', 'month'); // default: month

        $historyRecords = collect();
        if ($selectedEmployee) {
            $query = Attendance::where('employee_id', $selectedEmployee->id);

            if ($period === 'week') {
                $query->whereBetween('date', [
                    Carbon::now()->startOfWeek()->toDateString(),
                    Carbon::now()->endOfWeek()->toDateString()
                ]);
            } elseif ($period === 'month') {
                $query->whereMonth('date', Carbon::now()->month)
                      ->whereYear('date', Carbon::now()->year);
            }

            $historyRecords = $query->orderBy('date', 'desc')
                ->orderBy('time_in', 'desc')
                ->paginate(15)
                ->withQueryString();
        }

        return view('mobile_riwayat', compact(
            'selectedEmployee',
            'historyRecords',
            'period'
        ));
    }

    // 4. Mobile Tab Profil Pegawai
    public function mobileProfil(Request $request)
    {
        $selectedEmployee = $this->getAuthenticatedEmployee($request);

        return view('mobile_profil', compact(
            'selectedEmployee'
        ));
    }

    // Halaman Monitor Presensi Hari Ini
    public function monitor(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $institutions = Institution::where('is_active', true)->get();

        $query = Attendance::with(['employee', 'institution'])
            ->whereDate('date', $today);

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('time_in', 'desc')->paginate(20)->withQueryString();

        // Rekap ringkas hari ini
        $allToday = Attendance::whereDate('date', $today)->get();
        $hadirCount = $allToday->where('status', 'hadir')->count();
        $terlambatCount = $allToday->where('status', 'terlambat')->count();
        $izinCount = $allToday->whereIn('status', ['izin', 'sakit'])->count();
        $alpaCount = $allToday->where('status', 'alpa')->count();

        return view('admin.presensi', compact(
            'attendances',
            'institutions',
            'hadirCount',
            'terlambatCount',
            'izinCount',
            'alpaCount'
        ));
    }

    // Halaman Riwayat Presensi Komprehensif
    public function history(Request $request)
    {
        $institutions = Institution::where('is_active', true)->get();

        $query = Attendance::with(['employee', 'institution']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip_nidn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $records = $query->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.riwayat', compact('records', 'institutions'));
    }

    // API / Form Catat Presensi Langsung (Bisa untuk mobile/web check-in)
    public function checkIn(Request $request)
    {
        $employee = null;
        if (auth()->check() && auth()->user()->employee) {
            $employee = auth()->user()->employee;
        } elseif (auth()->check()) {
            $employee = Employee::where('email', auth()->user()->email)->first();
        }

        if (!$employee) {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
            ]);
            $employee = Employee::findOrFail($validated['employee_id']);
        }

        $validated = $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->toTimeString();

        // Cek apakah sudah absen hari ini
        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $employee->id, 'date' => $today],
            [
                'institution_id' => $employee->institution_id,
                'time_in' => $nowTime,
                'status' => 'hadir',
                'latitude_in' => $validated['latitude'] ?? null,
                'longitude_in' => $validated['longitude'] ?? null,
                'notes' => $validated['notes'] ?? 'Presensi masuk',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Presensi masuk berhasil dicatat.',
            'data' => $attendance,
        ]);
    }

    // Catat Presensi Pulang
    public function checkOut(Request $request)
    {
        $employee = null;
        if (auth()->check() && auth()->user()->employee) {
            $employee = auth()->user()->employee;
        } elseif (auth()->check()) {
            $employee = Employee::where('email', auth()->user()->email)->first();
        }

        if (!$employee) {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
            ]);
            $employee = Employee::findOrFail($validated['employee_id']);
        }

        $validated = $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $today = Carbon::today()->toDateString();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada catatan presensi masuk hari ini.',
            ], 422);
        }

        $attendance->update([
            'time_out' => Carbon::now()->toTimeString(),
            'latitude_out' => $validated['latitude'] ?? null,
            'longitude_out' => $validated['longitude'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Presensi pulang berhasil dicatat.',
            'data' => $attendance,
        ]);
    }
}
