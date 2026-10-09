@extends('layouts.mobile')

@section('title', 'Profil Pegawai | YABAT Attendance')

@section('content')
<!-- ==========================================
     HALAMAN PROFIL PEGAWAI MOBILE
     - Informasi Pegawai Lengkap
     - Jabatan & Institusi Naungan
     - Pengaturan Akun & Keamanan Sesi
     ========================================== -->

<!-- 1. Hero Card Profil Pegawai -->
<section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm text-center space-y-3">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-700 to-sky-500 text-white font-extrabold text-xl shadow-md shadow-blue-500/20 mx-auto">
        {{ strtoupper(substr($selectedEmployee->name, 0, 2)) }}
    </div>

    <div>
        <h2 class="text-base font-bold text-slate-900 tracking-tight">{{ $selectedEmployee->name }}</h2>
        <p class="text-xs text-blue-600 font-semibold mt-0.5">{{ $selectedEmployee->position }}</p>
        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">
            NIP/NIDN: {{ $selectedEmployee->nip_nidn }}
        </span>
    </div>

    <div class="pt-3 border-t border-slate-100 flex items-center justify-around text-xs">
        <div>
            <span class="text-slate-400 block text-[10px]">Status Kerja</span>
            <span class="font-bold text-emerald-600 capitalize">{{ $selectedEmployee->employment_status ?? 'Tetap' }}</span>
        </div>
        <div class="h-6 w-px bg-slate-200"></div>
        <div>
            <span class="text-slate-400 block text-[10px]">Bergabung Sejak</span>
            <span class="font-bold text-slate-700 font-mono-num">
                {{ $selectedEmployee->join_date ? \Carbon\Carbon::parse($selectedEmployee->join_date)->format('Y') : '2020' }}
            </span>
        </div>
    </div>
</section>

<!-- 2. Informasi Detail Institusi & Kontak -->
<section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
    <div class="pb-1 border-b border-slate-100">
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Lembaga Naungan</span>
        <div class="text-xs font-bold text-slate-800">{{ $selectedEmployee->institution->name ?? '-' }}</div>
    </div>

    <div class="space-y-2.5 text-xs">
        <div class="flex justify-between items-center py-1 border-b border-slate-50">
            <span class="text-slate-400">Kode Unit:</span>
            <span class="font-mono font-semibold text-slate-800">{{ $selectedEmployee->institution->code ?? '-' }}</span>
        </div>
        <div class="flex justify-between items-center py-1 border-b border-slate-50">
            <span class="text-slate-400">Kategori:</span>
            <span class="font-medium text-slate-800">{{ $selectedEmployee->institution->category ?? '-' }}</span>
        </div>
        <div class="flex justify-between items-center py-1 border-b border-slate-50">
            <span class="text-slate-400">Radius Presensi:</span>
            <span class="font-mono font-semibold text-blue-600">{{ $selectedEmployee->institution->radius_meters ?? 100 }} Meter</span>
        </div>
        <div class="flex justify-between items-center py-1 border-b border-slate-50">
            <span class="text-slate-400">Email Pegawai:</span>
            <span class="font-medium text-slate-800">{{ $selectedEmployee->email ?? 'pegawai@anakbangsa.org' }}</span>
        </div>
        <div class="flex justify-between items-center py-1 border-b border-slate-50">
            <span class="text-slate-400">Nomor Telepon:</span>
            <span class="font-mono font-medium text-slate-800">{{ $selectedEmployee->phone ?? '-' }}</span>
        </div>
        <div class="pt-1 text-[11px] text-slate-500">
            <span class="text-slate-400 block mb-0.5">Alamat Kampus / Sekolah:</span>
            <span>{{ $selectedEmployee->institution->address ?? 'Pulonas Baru, Kec. Lawe Bulan, Kabupaten Aceh Tenggara, Aceh' }}</span>
        </div>
    </div>
</section>

<!-- 3. Pengaturan Akun & Akses Sistem -->
<section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
    <div class="pb-1 border-b border-slate-100">
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Keamanan &amp; Akun</span>
        <div class="text-xs font-bold text-slate-800">Preferensi &amp; Perangkat</div>
    </div>

    <div class="space-y-2 text-xs">
        <!-- Status Izin Lokasi Perangkat -->
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
            <div>
                <div class="font-semibold text-slate-800 text-[11px]">Sensor Lokasi GPS</div>
                <div class="text-[10px] text-slate-400">Wajib aktif saat presensi</div>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                Aktif
            </span>
        </div>

        <!-- Mode Presensi -->
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
            <div>
                <div class="font-semibold text-slate-800 text-[11px]">Mode Validasi</div>
                <div class="text-[10px] text-slate-400">Geofencing Titik Radius</div>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                Tanpa Kamera
            </span>
        </div>

        <!-- Tombol Akses Dashboard Admin -->
        <div class="pt-2">
            <a href="{{ route('admin.dashboard') }}" 
               class="w-full py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center justify-center gap-2 transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Akses Panel Admin Yayasan</span>
            </a>
        </div>

        <!-- Tombol Keluar Sistem -->
        <form method="POST" action="{{ route('logout') }}" class="pt-1">
            @csrf
            <button type="submit" 
                    class="w-full py-2.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold flex items-center justify-center gap-2 border border-rose-200 transition">
                <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Keluar / Ganti Akun</span>
            </button>
        </form>
    </div>
</section>
@endsection
