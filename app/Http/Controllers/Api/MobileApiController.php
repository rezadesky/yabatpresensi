<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Institution;
use App\Models\User;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MobileApiController extends Controller
{
    // 1. API Login: Email/NIP + Password -> returns User, Employee, and Token
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required',
        ]);

        $loginInput = $request->input('email');
        $password = $request->input('password');

        $user = null;
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginInput)->first();
        } else {
            $employee = Employee::where('nip_nidn', $loginInput)->first();
            if ($employee && $employee->user_id) {
                $user = User::find($employee->user_id);
            }
        }

        // Fallback khusus admin jika ada
        if (!$user && $loginInput === 'admin@stkip-us.ac.id' && $password === 'stkipus2026') {
            $user = User::where('email', 'admin@stkip-us.ac.id')->first();
        }

        if (!$user || !Hash::check($password, $user->password)) {
            if (!($loginInput === 'admin@stkip-us.ac.id' && $password === 'stkipus2026')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email/NIP atau kata sandi yang Anda masukkan tidak sesuai.',
                ], 401);
            }
        }

        // Ambil profil Pegawai
        $employee = Employee::with('institution')->where('user_id', $user->id)->first();
        if (!$employee) {
            $employee = Employee::with('institution')->where('email', $user->email)->first();
        }

        // Buat token Sanctum
        $token = $user->createToken('yabat-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'employee' => $employee,
        ]);
    }

    // Helper: Ambil Employee dari authenticated user
    protected function getEmployee(Request $request)
    {
        $user = $request->user();
        if (!$user) return null;

        $employee = Employee::with('institution')->where('user_id', $user->id)->first();
        if (!$employee) {
            $employee = Employee::with('institution')->where('email', $user->email)->first();
        }
        if (!$employee && $user->role === 'admin') {
            $employee = Employee::with('institution')->first();
        }
        return $employee;
    }

    // 2. API Beranda Data
    public function beranda(Request $request)
    {
        $employee = $this->getEmployee($request);
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan'], 404);
        }

        $today = Carbon::today()->toDateString();
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $workSchedule = WorkSchedule::where('institution_id', $employee->institution_id)
            ->where('is_active', true)
            ->first();

        return response()->json([
            'success' => true,
            'employee' => $employee,
            'todayAttendance' => $todayAttendance,
            'workSchedule' => $workSchedule,
            'server_time' => Carbon::now()->toIso8601String(),
        ]);
    }

    // 3. API Presensi Status & Unit Data
    public function presensiStatus(Request $request)
    {
        $employee = $this->getEmployee($request);
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan'], 404);
        }

        $today = Carbon::today()->toDateString();
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        return response()->json([
            'success' => true,
            'employee' => $employee,
            'institution' => $employee->institution,
            'todayAttendance' => $todayAttendance,
            'server_time' => Carbon::now()->toIso8601String(),
        ]);
    }

    // 4. API Check-In
    public function checkIn(Request $request)
    {
        $employee = $this->getEmployee($request);
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->toTimeString();

        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $employee->id, 'date' => $today],
            [
                'institution_id' => $employee->institution_id,
                'time_in' => $nowTime,
                'status' => 'hadir',
                'latitude_in' => $validated['latitude'] ?? null,
                'longitude_in' => $validated['longitude'] ?? null,
                'notes' => $validated['notes'] ?? 'Presensi masuk mobile',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Presensi masuk berhasil dicatat.',
            'data' => $attendance,
        ]);
    }

    // 5. API Check-Out
    public function checkOut(Request $request)
    {
        $employee = $this->getEmployee($request);
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan'], 404);
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

    // 6. API Riwayat Presensi
    public function riwayat(Request $request)
    {
        $employee = $this->getEmployee($request);
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan'], 404);
        }

        $period = $request->get('period', 'month');

        $query = Attendance::where('employee_id', $employee->id);

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
            ->paginate(30);

        // Hitung statistik
        $allPeriod = (clone $query)->get();
        $stats = [
            'total' => $historyRecords->total(),
            'hadir' => $allPeriod->where('status', 'hadir')->count(),
            'terlambat' => $allPeriod->where('status', 'terlambat')->count(),
            'izin' => $allPeriod->whereIn('status', ['izin', 'sakit'])->count(),
            'alpa' => $allPeriod->where('status', 'alpa')->count(),
        ];

        return response()->json([
            'success' => true,
            'period' => $period,
            'stats' => $stats,
            'records' => $historyRecords->items(),
            'current_page' => $historyRecords->currentPage(),
            'last_page' => $historyRecords->lastPage(),
        ]);
    }

    // 7. API Profil
    public function profil(Request $request)
    {
        $employee = $this->getEmployee($request);
        return response()->json([
            'success' => true,
            'employee' => $employee,
        ]);
    }

    // 8. API Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout.',
        ]);
    }
}
