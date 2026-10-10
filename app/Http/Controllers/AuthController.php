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
            // Jika pegawai mencoba buka di browser, arahkan ke info aplikasi mobile
            return redirect()->route('mobile.info');
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

        // 1. Cek user admin
        $user = null;
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = \App\Models\User::where('email', $loginInput)->first();
        } else {
            $employee = \App\Models\Employee::where('nip_nidn', $loginInput)->first();
            if ($employee && $employee->user_id) {
                $user = \App\Models\User::find($employee->user_id);
            }
        }

        // Fallback khusus Admin STKIP
        if (!$user && $loginInput === 'admin@stkip-us.ac.id' && $password === 'stkipus2026') {
            $user = \App\Models\User::where('email', 'admin@stkip-us.ac.id')->first();
        }

        // Verifikasi kredensial
        if ($user && (\Illuminate\Support\Facades\Hash::check($password, $user->password) || ($loginInput === 'admin@stkip-us.ac.id' && $password === 'stkipus2026'))) {
            // JIKA BUKAN ADMIN: Tolak login di browser web, beri informasi untuk pakai aplikasi APK
            if ($user->role !== 'admin') {
                return back()->withErrors([
                    'email' => 'Akun Pegawai hanya dapat digunakan melalui Aplikasi Mobile Resmi YABAT PRESENSI (Android APK). Silakan buka aplikasi di HP Anda.',
                ])->onlyInput('email');
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email/Akun atau kata sandi yang Anda masukkan tidak sesuai.',
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
