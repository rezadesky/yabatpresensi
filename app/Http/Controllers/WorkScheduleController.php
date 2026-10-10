<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{
    public function index()
    {
        $schedules = WorkSchedule::with('institution')
            ->orderBy('id', 'asc')
            ->get();
        $institutions = Institution::where('is_active', true)->get();

        return view('admin.jadwal', compact('schedules', 'institutions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution_id' => 'nullable|exists:institutions,id',
            'name' => 'required|string|max:255',
            'day_of_week' => 'required',
            'time_in' => 'required',
            'time_out' => 'required',
            'late_tolerance_minutes' => 'required|integer|min:0',
        ]);

        if (is_array($validated['day_of_week'])) {
            $validated['day_of_week'] = implode(', ', $validated['day_of_week']);
        }

        WorkSchedule::create($validated);

        return redirect()->route('admin.jadwal')->with('success', 'Jadwal kerja berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $schedule = WorkSchedule::findOrFail($id);

        $validated = $request->validate([
            'institution_id' => 'nullable|exists:institutions,id',
            'name' => 'required|string|max:255',
            'day_of_week' => 'required',
            'time_in' => 'required',
            'time_out' => 'required',
            'late_tolerance_minutes' => 'required|integer|min:0',
        ]);

        if (is_array($validated['day_of_week'])) {
            $validated['day_of_week'] = implode(', ', $validated['day_of_week']);
        }

        $schedule->update($validated);

        return redirect()->route('admin.jadwal')->with('success', 'Jadwal kerja berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $schedule = WorkSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('admin.jadwal')->with('success', 'Jadwal kerja berhasil dihapus.');
    }
}
