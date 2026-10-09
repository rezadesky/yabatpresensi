<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::withCount('employees')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.institusi', compact('institutions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:institutions,code',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius_meters' => 'required|integer|min:10',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'head_name' => 'nullable|string|max:255',
        ]);

        Institution::create($validated);

        return redirect()->route('admin.institusi')->with('success', 'Unit institusi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $institution = Institution::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:institutions,code,' . $institution->id,
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius_meters' => 'required|integer|min:10',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'head_name' => 'nullable|string|max:255',
        ]);

        $institution->update($validated);

        return redirect()->route('admin.institusi')->with('success', 'Unit institusi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $institution = Institution::findOrFail($id);
        $institution->delete();

        return redirect()->route('admin.institusi')->with('success', 'Unit institusi berhasil dihapus.');
    }
}
