<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $institutions = \App\Models\Institution::where('is_active', true)->get();

        return view('admin.pengaturan', compact('settings', 'institutions'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method', 'institutions', 'unified_latitude', 'unified_longitude', 'unified_radius_meters']);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        // Jika diset 1 titik koordinat & radius bersama untuk satu wilayah
        if ($request->filled('unified_latitude') && $request->filled('unified_longitude') && $request->filled('unified_radius_meters')) {
            \App\Models\Institution::query()->update([
                'latitude' => $request->unified_latitude,
                'longitude' => $request->unified_longitude,
                'radius_meters' => $request->unified_radius_meters,
            ]);
        } elseif ($request->has('institutions') && is_array($request->institutions)) {
            foreach ($request->institutions as $id => $val) {
                $inst = \App\Models\Institution::find($id);
                if ($inst) {
                    $inst->update([
                        'latitude' => $val['latitude'],
                        'longitude' => $val['longitude'],
                        'radius_meters' => $val['radius_meters'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.pengaturan')->with('success', 'Pengaturan sistem, koordinat GPS dan radius presensi wilayah berhasil disimpan.');
    }
}
