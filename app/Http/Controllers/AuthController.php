<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('mobile.beranda');
        }
        return view('welcome');
    }

    public function login(Request $request)
    {
        $input = $request->validate([
            'email' => 'required|string',
            'password' => 'required|min:4',
        ]);

        $loginInput = $input['email'];
        $password = $input['password'];
        $remember = $request->boolean('remember');

        // 1. Cek apakah input berupa email atau NIP pegawai
        $user = null;
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = \App\Models\User::where('email', $loginInput)->first();
        } else {
            // Cari pegawai berdasarkan NIP/NIDN
            $employee = \App\Models\Employee::where('nip_nidn', $loginInput)->first();
            if ($employee && $employee->user_id) {
                $user = \App\Models\User::find($employee->user_id);
            }
        }

        // 2. Fallback khusus Admin STKIP
        if (!$user && $loginInput === 'admin@stkip-us.ac.id' && $password === 'stkipus2026') {
            $user = \App\Models\User::where('email', 'admin@stkip-us.ac.id')->first();
        }

        // 3. Verifikasi Password dan Login
        if ($user && (\Illuminate\Support\Facades\Hash::check($password, $user->password) || ($loginInput === 'admin@stkip-us.ac.id' && $password === 'stkipus2026'))) {
            Auth::login($user, $remember);
            $request->session()->regenerate();

            // Redirect sesuai Role (Eksplisit tanpa intended agar pegawai tidak terlempar ke URL admin)
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('mobile.beranda');
            }
        }

        // 4. Standar Laravel Auth attempt jika belum terdeteksi
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        if (Auth::attempt([$fieldType => $loginInput, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            $loggedUser = Auth::user();
            if ($loggedUser->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('mobile.beranda');
        }

        return back()->withErrors([
            'email' => 'Email/NIP atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
