<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Institution;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('institution');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip_nidn', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        if ($request->filled('employment_status')) {
            $query->where('employment_status', $request->employment_status);
        }

        $employees = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();
        $institutions = Institution::where('is_active', true)->get();

        return view('admin.pegawai', compact('employees', 'institutions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'nip_nidn' => 'required|unique:employees,nip_nidn',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'gender' => 'required|in:L,P',
            'position' => 'required|string|max:255',
            'employment_status' => 'required|in:tetap,kontrak,honorer',
            'join_date' => 'nullable|date',
            'address' => 'nullable|string',
        ]);

        Employee::create($validated);

        return redirect()->route('admin.pegawai')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'nip_nidn' => 'required|unique:employees,nip_nidn,' . $employee->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'gender' => 'required|in:L,P',
            'position' => 'required|string|max:255',
            'employment_status' => 'required|in:tetap,kontrak,honorer',
            'join_date' => 'nullable|date',
            'address' => 'nullable|string',
        ]);

        $employee->update($validated);

        return redirect()->route('admin.pegawai')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('admin.pegawai')->with('success', 'Pegawai berhasil dihapus.');
    }
}
