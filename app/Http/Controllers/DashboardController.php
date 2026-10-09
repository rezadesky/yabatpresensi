<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Institution;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $totalEmployees = Employee::where('is_active', true)->count();
        $totalInstitutions = Institution::where('is_active', true)->count();

        $todayAttendances = Attendance::with(['employee', 'institution'])
            ->whereDate('date', $today)
            ->get();

        $presentCount = $todayAttendances->whereIn('status', ['hadir', 'terlambat'])->count();
        $lateCount = $todayAttendances->where('status', 'terlambat')->count();
        $leaveCount = $todayAttendances->whereIn('status', ['izin', 'sakit'])->count();
        
        $attendanceRate = $totalEmployees > 0 
            ? round(($presentCount / $totalEmployees) * 100, 1) 
            : 0;

        // Institution Breakdown
        $institutions = Institution::withCount('employees')
            ->where('is_active', true)
            ->get()
            ->map(function ($inst) use ($today) {
                $todayInstAttendances = Attendance::where('institution_id', $inst->id)
                    ->whereDate('date', $today)
                    ->get();

                $instPresent = $todayInstAttendances->whereIn('status', ['hadir', 'terlambat'])->count();
                $rate = $inst->employees_count > 0 
                    ? round(($instPresent / $inst->employees_count) * 100, 1) 
                    : 0;

                return [
                    'id' => $inst->id,
                    'name' => $inst->name,
                    'category' => $inst->category,
                    'employees_count' => $inst->employees_count,
                    'present_count' => $instPresent,
                    'rate' => $rate,
                ];
            });

        // 7-day trend
        $weeklyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $dayName = Carbon::today()->subDays($i)->locale('id')->isoFormat('ddd');
            $count = Attendance::whereDate('date', $date)->whereIn('status', ['hadir', 'terlambat'])->count();
            $percentage = $totalEmployees > 0 ? min(100, round(($count / $totalEmployees) * 100)) : 0;
            $weeklyStats[] = [
                'day' => $dayName,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        // Recent Activity
        $recentActivities = Attendance::with(['employee', 'institution'])
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->take(6)
            ->get();

        $rawInstitutions = Institution::where('is_active', true)->get();

        return view('admin.dashboard', compact(
            'totalEmployees',
            'totalInstitutions',
            'presentCount',
            'lateCount',
            'leaveCount',
            'attendanceRate',
            'institutions',
            'rawInstitutions',
            'weeklyStats',
            'recentActivities'
        ));
    }

    // Update Lokasi Absen (Latitude, Longitude, Radius) dari Dashboard (1 Titik Bersama untuk seluruh institusi)
    public function updateLocations(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:10|max:5000',
        ]);

        Institution::query()->update([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'radius_meters' => $validated['radius_meters'],
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Titik lokasi GPS wilayah dan batas radius berhasil diperbarui untuk seluruh 3 unit institusi.');
    }
}
