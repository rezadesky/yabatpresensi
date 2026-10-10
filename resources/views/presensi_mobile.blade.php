<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Presensi Mobile GPS | YABAT Attendance</title>

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .font-mono-num {
            font-family: 'JetBrains Mono', monospace;
            font-variant-numeric: tabular-nums;
        }
        .pb-safe {
            padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 5.5rem);
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased bg-slate-100 selection:bg-blue-600 selection:text-white">

    <!-- Container Utama: Mobile First (Maksimal Lebar HP di Desktop) -->
    <div class="min-h-full max-w-md mx-auto bg-slate-50 border-x border-slate-200/70 flex flex-col relative shadow-2xl pb-safe">

        <!-- ==========================================
             A. HEADER PEGAWAI & IDENTITAS RESMI YABAT
             ========================================== -->
        <header class="bg-gradient-to-b from-slate-950 via-slate-900 to-slate-900 text-white px-5 pt-6 pb-6 rounded-b-3xl shadow-lg relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-blue-600/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-sky-500/15 rounded-full blur-xl pointer-events-none"></div>

            <div class="relative z-10 space-y-4">
                <!-- Brand Bar -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('logo.png') }}" alt="Logo YABAT" class="w-8 h-8 object-contain drop-shadow">
                        <div>
                            <span class="text-xs font-bold tracking-wider text-white uppercase block leading-tight">YABAT ATTENDANCE</span>
                            <span class="text-[10px] text-blue-300 font-medium">Aceh Tenggara &bull; GPS Geofencing</span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-800/90 border border-slate-700/80 text-[10px] font-medium text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>GPS Aktif</span>
                    </span>
                </div>

                <!-- Pemilih Pegawai Langsung (Database) -->
                <div class="bg-slate-800/70 border border-slate-700/60 backdrop-blur-sm rounded-2xl p-3.5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] uppercase font-semibold text-slate-400 tracking-wider">Akun Pegawai Aktif</span>
                        <span class="text-[9px] bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 px-2 py-0.5 rounded-md font-medium">Database Live</span>
                    </div>

                    <!-- Pilihan Pegawai dari DB -->
                    <div class="relative">
                        <select id="employeeSelector" class="w-full bg-slate-900/90 border border-slate-700 text-white text-xs rounded-xl px-3 py-2 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium pr-8 cursor-pointer">
                            @foreach ($employees as $emp)
                                <option 
                                    value="{{ $emp->id }}" 
                                    data-institution-name="{{ $emp->institution->name ?? 'YABAT' }}"
                                    data-lat="{{ $emp->institution->latitude ?? 3.4883 }}"
                                    data-lng="{{ $emp->institution->longitude ?? 97.8085 }}"
                                    data-radius="{{ $emp->institution->radius_meters ?? 100 }}"
                                    data-position="{{ $emp->position }}"
                                    data-nip="{{ $emp->nip_nidn }}"
                                    data-name="{{ $emp->name }}"
                                    {{ ($selectedEmployee && $selectedEmployee->id == $emp->id) ? 'selected' : '' }}>
                                    [{{ $emp->institution->code ?? 'UNIT' }}] {{ $emp->name }} ({{ $emp->position }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2.5 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    <!-- Detail Profil Pegawai Terpilih -->
                    <div class="mt-3 pt-2.5 border-t border-slate-700/60 flex items-center justify-between text-xs">
                        <div class="truncate">
                            <div id="empName" class="font-bold text-white text-sm tracking-tight truncate">{{ $selectedEmployee->name ?? '-' }}</div>
                            <div id="empRole" class="text-[11px] text-slate-300 truncate">{{ $selectedEmployee->position ?? '-' }}</div>
                        </div>
                        <div class="text-right flex-shrink-0 pl-2">
                            <div id="empInstitution" class="text-[11px] font-semibold text-blue-300 truncate">{{ $selectedEmployee->institution->name ?? '-' }}</div>
                            <div id="empNip" class="text-[10px] font-mono-num text-slate-400">NIP. {{ $selectedEmployee->nip_nidn ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Hari & Tanggal Resmi -->
                <div class="flex items-center justify-between text-xs px-1 text-slate-300">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span id="currentDateFormatted" class="font-medium text-slate-200">Memuat tanggal...</span>
                    </div>
                    <span class="text-[11px] text-slate-400">Aceh Tenggara (WIB)</span>
                </div>
            </div>
        </header>

        <!-- MAIN BODY CONTENT -->
        <main class="flex-1 px-4 py-5 space-y-4">

            <!-- ==========================================
                 B. JAM & JADWAL KERJA
                 ========================================== -->
            <section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Jam Digital Waktu Nyata</span>
                    <span id="workStatusBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Jam Kerja Aktif</span>
                    </span>
                </div>

                <!-- Clock Display -->
                <div class="text-center py-1">
                    <div id="realtimeClock" class="text-4xl font-extrabold text-slate-900 tracking-tight font-mono-num">
                        --:--:--
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Waktu Indonesia Barat (WIB)</div>
                </div>

                <!-- Jadwal Shift In / Out -->
                <div class="grid grid-cols-2 gap-2.5 pt-2 border-t border-slate-100 text-xs">
                    <div class="bg-slate-50/80 p-2.5 rounded-xl border border-slate-200/60">
                        <div class="text-[10px] text-slate-500 font-medium flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Jam Masuk
                        </div>
                        <div class="text-sm font-bold text-slate-800 font-mono-num mt-0.5">07:30 WIB</div>
                        <div class="text-[10px] text-slate-400">Toleransi s/d 07:45</div>
                    </div>
                    <div class="bg-slate-50/80 p-2.5 rounded-xl border border-slate-200/60">
                        <div class="text-[10px] text-slate-500 font-medium flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Jam Pulang
                        </div>
                        <div class="text-sm font-bold text-slate-800 font-mono-num mt-0.5">16:00 WIB</div>
                        <div class="text-[10px] text-slate-400">Senin - Kamis &amp; Sabtu</div>
                    </div>
                </div>
            </section>

            <!-- ==========================================
                 C. STATUS PRESENSI HARI INI
                 ========================================== -->
            <section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Status Kehadiran Hari Ini</span>
                    <span id="overallAttendanceSummary" class="text-[11px] font-semibold {{ $todayAttendance ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-slate-600 bg-slate-100' }} px-2 py-0.5 rounded-md">
                        @if ($todayAttendance)
                            {{ ucfirst($todayAttendance->status) }} (Tercatat)
                        @else
                            Belum Presensi
                        @endif
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <!-- Presensi Masuk Status -->
                    <div class="p-3 rounded-xl border bg-slate-50/50 border-slate-200/80 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-medium text-slate-500">Presensi Masuk</span>
                            <span id="badgeIn" class="w-2 h-2 rounded-full {{ ($todayAttendance && $todayAttendance->time_in) ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                        </div>
                        <div id="textTimeIn" class="text-base font-bold {{ ($todayAttendance && $todayAttendance->time_in) ? 'text-emerald-600' : 'text-slate-700' }} font-mono-num">
                            {{ ($todayAttendance && $todayAttendance->time_in) ? \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i') . ' WIB' : '--:--' }}
                        </div>
                        <div id="descTimeIn" class="text-[10px] text-slate-400">
                            {{ ($todayAttendance && $todayAttendance->time_in) ? 'Terverifikasi lokasi GPS' : 'Menunggu verifikasi GPS' }}
                        </div>
                    </div>

                    <!-- Presensi Pulang Status -->
                    <div class="p-3 rounded-xl border bg-slate-50/50 border-slate-200/80 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-medium text-slate-500">Presensi Pulang</span>
                            <span id="badgeOut" class="w-2 h-2 rounded-full {{ ($todayAttendance && $todayAttendance->time_out) ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                        </div>
                        <div id="textTimeOut" class="text-base font-bold {{ ($todayAttendance && $todayAttendance->time_out) ? 'text-emerald-600' : 'text-slate-700' }} font-mono-num">
                            {{ ($todayAttendance && $todayAttendance->time_out) ? \Carbon\Carbon::parse($todayAttendance->time_out)->format('H:i') . ' WIB' : '--:--' }}
                        </div>
                        <div id="descTimeOut" class="text-[10px] text-slate-400">
                            {{ ($todayAttendance && $todayAttendance->time_out) ? 'Telah presensi pulang' : 'Tersedia jam pulang' }}
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==========================================
                 D. LOKASI GPS (HTML5 GEOLOCATION API)
                 ========================================== -->
            <section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-slate-900 leading-tight">Sensor Lokasi GPS</h2>
                            <p class="text-[10px] text-slate-400">Geolocation Perangkat Pegawai</p>
                        </div>
                    </div>

                    <!-- GPS Status Pill -->
                    <span id="gpsStatusPill" class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                        Belum Diperiksa
                    </span>
                </div>

                <!-- Koordinat & Akurasi Display -->
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/70 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 text-[11px]">Latitude:</span>
                        <span id="displayLat" class="font-mono-num font-semibold text-slate-800 text-[11px]">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 text-[11px]">Longitude:</span>
                        <span id="displayLng" class="font-mono-num font-semibold text-slate-800 text-[11px]">-</span>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                        <span class="text-slate-400 text-[11px]">Akurasi Sinyal GPS:</span>
                        <span id="displayAccuracy" class="font-mono-num text-[11px] font-semibold text-slate-600">-</span>
                    </div>
                </div>

                <!-- Error Notice Box (Hidden by default) -->
                <div id="gpsErrorBox" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-rose-800">
                        <svg class="w-4 h-4 flex-shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span id="gpsErrorTitle">Gagal Mengambil Lokasi</span>
                    </div>
                    <p id="gpsErrorMessage" class="text-[11px] text-rose-600 leading-relaxed">
                        Izin lokasi ditolak atau sinyal GPS tidak terdeteksi. Silakan aktifkan GPS perangkat Anda.
                    </p>
                </div>

                <!-- Tombol Periksa Lokasi GPS Perangkat -->
                <button 
                    type="button" 
                    id="btnCheckLocation" 
                    class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white text-xs font-semibold flex items-center justify-center gap-2 shadow-sm transition">
                    <svg id="btnGpsIcon" class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 16v-4m8-4h-4M4 12h4" />
                    </svg>
                    <span id="btnCheckLocationText">Periksa Lokasi Saya Sekarang</span>
                </button>
            </section>

            <!-- ==========================================
                 E. VALIDASI AREA PRESENSI (GEOFENCING)
                 ========================================== -->
            <section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Verifikasi Area Kerja</span>
                        <div id="targetInstitutionTitle" class="text-xs font-bold text-slate-800 mt-0.5">{{ $selectedEmployee->institution->name ?? 'Unit Institusi' }}</div>
                    </div>
                    <!-- Badge Status Area -->
                    <span id="areaStatusBadge" class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200">
                        Menunggu GPS
                    </span>
                </div>

                <!-- Info Jarak dan Radius -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <div class="text-[10px] text-slate-400 font-medium">Jarak ke Unit Kerja</div>
                        <div id="displayDistance" class="text-sm font-bold text-slate-900 font-mono-num mt-0.5">-</div>
                        <div class="text-[10px] text-slate-400">Dihitung otomatis (Haversine)</div>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <div class="text-[10px] text-slate-400 font-medium">Batas Maksimal Radius</div>
                        <div id="displayAllowedRadius" class="text-sm font-bold text-blue-600 font-mono-num mt-0.5">{{ $selectedEmployee->institution->radius_meters ?? 100 }} Meter</div>
                        <div class="text-[10px] text-slate-400">Kebijakan resmi yayasan</div>
                    </div>
                </div>
            </section>

            <!-- ==========================================
                 F. TOMBOL PRESENSI LANGSUNG (TERKONEKSI DATABASE)
                 ========================================== -->
            <section class="space-y-2 pt-1">
                <div class="grid grid-cols-2 gap-3">
                    <!-- Tombol Masuk -->
                    <button 
                        type="button" 
                        id="btnCheckIn" 
                        disabled 
                        class="py-3 px-3 rounded-2xl bg-slate-300 text-slate-500 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Presensi Masuk</span>
                        </div>
                        <span id="btnCheckInSub" class="text-[9px] font-normal opacity-80">
                            {{ ($todayAttendance && $todayAttendance->time_in) ? 'Sudah presensi masuk' : 'Perlu periksa lokasi' }}
                        </span>
                    </button>

                    <!-- Tombol Pulang -->
                    <button 
                        type="button" 
                        id="btnCheckOut" 
                        disabled 
                        class="py-3 px-3 rounded-2xl bg-slate-300 text-slate-500 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Presensi Pulang</span>
                        </div>
                        <span id="btnCheckOutSub" class="text-[9px] font-normal opacity-80">
                            {{ ($todayAttendance && $todayAttendance->time_out) ? 'Sudah presensi pulang' : 'Menunggu absen masuk' }}
                        </span>
                    </button>
                </div>

                <!-- Feedback Toast Interaktif -->
                <div id="actionToast" class="hidden p-3 rounded-xl bg-slate-900 text-white text-xs text-center font-medium shadow-lg animate-fade">
                    <span id="toastMessage">Memproses presensi...</span>
                </div>
            </section>

            <!-- ==========================================
                 G. RIWAYAT PRESENSI PEGAWAI DARI DATABASE
                 ========================================== -->
            <section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Riwayat Kehadiran</span>
                        <div class="text-xs font-bold text-slate-800">Catatan Presensi Terkini</div>
                    </div>
                    <span class="text-[9px] bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded font-medium">Data Nyata</span>
                </div>

                <div id="attendanceHistoryContainer" class="divide-y divide-slate-100 text-xs">
                    @forelse ($recentAttendances as $rec)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-slate-800">
                                    {{ \Carbon\Carbon::parse($rec->date)->isoFormat('dddd, D MMMM Y') }}
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $rec->time_in ? \Carbon\Carbon::parse($rec->time_in)->format('H:i') . ' WIB' : '--:--' }}
                                    - 
                                    {{ $rec->time_out ? \Carbon\Carbon::parse($rec->time_out)->format('H:i') . ' WIB' : 'Belum pulang' }}
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ $rec->status === 'hadir' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($rec->status === 'terlambat' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600') }}">
                                    {{ ucfirst($rec->status) }}
                                </span>
                                @if ($rec->latitude_in)
                                    <div class="text-[10px] text-slate-400 font-mono-num mt-0.5">GPS Terverifikasi</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-slate-400">
                            Belum ada riwayat presensi tersimpan untuk pegawai ini.
                        </div>
                    @endforelse
                </div>
            </section>
        </main>

        <!-- ==========================================
             H. NAVIGASI BAWAH (BOTTOM NAVBAR MOBILE)
             ========================================== -->
        <nav class="fixed bottom-0 left-0 right-0 z-30 max-w-md mx-auto bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-4 py-2 shadow-lg">
            <div class="flex items-center justify-around text-[10px] font-semibold text-slate-400">
                <!-- Beranda -->
                <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-1 py-1 px-3 text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Beranda</span>
                </a>

                <!-- Presensi (Active Tab) -->
                <a href="{{ route('presensi.mobile') }}" class="flex flex-col items-center gap-1 py-1 px-3 text-blue-600 transition">
                    <div class="relative">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-blue-600"></span>
                    </div>
                    <span class="text-blue-600 font-bold">Presensi</span>
                </a>

                <!-- Riwayat -->
                <a href="{{ route('admin.riwayat') }}" class="flex flex-col items-center gap-1 py-1 px-3 text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Riwayat</span>
                </a>

                <!-- Profil Pegawai -->
                <a href="{{ route('admin.pegawai') }}" class="flex flex-col items-center gap-1 py-1 px-3 text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Pegawai</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- ==========================================
         JAVASCRIPT LOGIC (LIVE PRESENSI & DATABASE)
         ========================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // State Data Pegawai Aktif
            const employeeSelector = document.getElementById('employeeSelector');
            let currentEmployee = getSelectedEmployeeData();

            let userCoordinates = null; // { lat, lng, accuracy }
            let calculatedDistance = null; // in meters
            let isInsideRadius = false;
            let hasCheckedIn = {{ ($todayAttendance && $todayAttendance->time_in) ? 'true' : 'false' }};
            let hasCheckedOut = {{ ($todayAttendance && $todayAttendance->time_out) ? 'true' : 'false' }};

            // DOM Elements
            const empName = document.getElementById('empName');
            const empRole = document.getElementById('empRole');
            const empInstitution = document.getElementById('empInstitution');
            const empNip = document.getElementById('empNip');
            const targetInstitutionTitle = document.getElementById('targetInstitutionTitle');
            const displayAllowedRadius = document.getElementById('displayAllowedRadius');

            const realtimeClock = document.getElementById('realtimeClock');
            const currentDateFormatted = document.getElementById('currentDateFormatted');

            const gpsStatusPill = document.getElementById('gpsStatusPill');
            const displayLat = document.getElementById('displayLat');
            const displayLng = document.getElementById('displayLng');
            const displayAccuracy = document.getElementById('displayAccuracy');
            const gpsErrorBox = document.getElementById('gpsErrorBox');
            const gpsErrorMessage = document.getElementById('gpsErrorMessage');
            const btnCheckLocation = document.getElementById('btnCheckLocation');
            const btnCheckLocationText = document.getElementById('btnCheckLocationText');
            const btnGpsIcon = document.getElementById('btnGpsIcon');

            const areaStatusBadge = document.getElementById('areaStatusBadge');
            const displayDistance = document.getElementById('displayDistance');

            const btnCheckIn = document.getElementById('btnCheckIn');
            const btnCheckInSub = document.getElementById('btnCheckInSub');
            const btnCheckOut = document.getElementById('btnCheckOut');
            const btnCheckOutSub = document.getElementById('btnCheckOutSub');
            const actionToast = document.getElementById('actionToast');
            const toastMessage = document.getElementById('toastMessage');

            const overallAttendanceSummary = document.getElementById('overallAttendanceSummary');
            const badgeIn = document.getElementById('badgeIn');
            const textTimeIn = document.getElementById('textTimeIn');
            const descTimeIn = document.getElementById('descTimeIn');
            const badgeOut = document.getElementById('badgeOut');
            const textTimeOut = document.getElementById('textTimeOut');
            const descTimeOut = document.getElementById('descTimeOut');

            // Helper dapatkan data pegawai terpilih
            function getSelectedEmployeeData() {
                const opt = employeeSelector.options[employeeSelector.selectedIndex];
                return {
                    id: opt.value,
                    name: opt.getAttribute('data-name'),
                    role: opt.getAttribute('data-position'),
                    institution: opt.getAttribute('data-institution-name'),
                    nip: opt.getAttribute('data-nip'),
                    lat: parseFloat(opt.getAttribute('data-lat')),
                    lng: parseFloat(opt.getAttribute('data-lng')),
                    radius: parseInt(opt.getAttribute('data-radius'), 10)
                };
            }

            // Realtime Clock (WIB)
            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                realtimeClock.textContent = `${hours}:${minutes}:${seconds}`;

                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                currentDateFormatted.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
            }
            setInterval(updateClock, 1000);
            updateClock();

            // Ubah Akun Pegawai (Reload data presensinya)
            employeeSelector.addEventListener('change', function () {
                const selectedId = this.value;
                window.location.href = `{{ route('presensi.mobile') }}?employee_id=${selectedId}`;
            });

            // Haversine Formula Jarak (Meter)
            function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
                const R = 6371e3;
                const rad = Math.PI / 180;
                const phi1 = lat1 * rad;
                const phi2 = lat2 * rad;
                const deltaPhi = (lat2 - lat1) * rad;
                const deltaLambda = (lon2 - lon1) * rad;

                const a = Math.sin(deltaPhi / 2) * Math.sin(deltaPhi / 2) +
                          Math.cos(phi1) * Math.cos(phi2) *
                          Math.sin(deltaLambda / 2) * Math.sin(deltaLambda / 2);

                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return Math.round(R * c);
            }

            // Proses Evaluasi Geofencing
            function processCoordinates(lat, lng, accuracy) {
                userCoordinates = { lat, lng, accuracy };

                displayLat.textContent = lat.toFixed(6);
                displayLng.textContent = lng.toFixed(6);
                displayAccuracy.textContent = `± ${Math.round(accuracy)} Meter`;

                gpsErrorBox.classList.add('hidden');
                gpsStatusPill.textContent = 'Lokasi Ditemukan';
                gpsStatusPill.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200';

                calculatedDistance = calculateHaversineDistance(lat, lng, currentEmployee.lat, currentEmployee.lng);
                displayDistance.textContent = `${calculatedDistance.toLocaleString()} Meter`;

                if (calculatedDistance <= currentEmployee.radius) {
                    isInsideRadius = true;
                    areaStatusBadge.textContent = 'Dalam Area Presensi';
                    areaStatusBadge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200';
                } else {
                    isInsideRadius = false;
                    areaStatusBadge.textContent = 'Di Luar Area Presensi';
                    areaStatusBadge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200';
                }

                updateActionButtons();
            }

            function updateActionButtons() {
                // Tombol Masuk
                if (hasCheckedIn) {
                    btnCheckIn.disabled = true;
                    btnCheckIn.className = 'py-3 px-3 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                    btnCheckInSub.textContent = 'Sudah presensi masuk';
                } else if (!userCoordinates) {
                    btnCheckIn.disabled = true;
                    btnCheckIn.className = 'py-3 px-3 rounded-2xl bg-slate-200 text-slate-400 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                    btnCheckInSub.textContent = 'Periksa lokasi dahulu';
                } else if (!isInsideRadius) {
                    btnCheckIn.disabled = true;
                    btnCheckIn.className = 'py-3 px-3 rounded-2xl bg-rose-100/70 text-rose-500 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                    btnCheckInSub.textContent = 'Di luar area presensi';
                } else {
                    btnCheckIn.disabled = false;
                    btnCheckIn.className = 'py-3 px-3 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-md shadow-blue-500/25 transition cursor-pointer';
                    btnCheckInSub.textContent = 'Kirim presensi masuk';
                }

                // Tombol Pulang
                if (hasCheckedOut) {
                    btnCheckOut.disabled = true;
                    btnCheckOut.className = 'py-3 px-3 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                    btnCheckOutSub.textContent = 'Sudah presensi pulang';
                } else if (!hasCheckedIn) {
                    btnCheckOut.disabled = true;
                    btnCheckOut.className = 'py-3 px-3 rounded-2xl bg-slate-200 text-slate-400 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                    btnCheckOutSub.textContent = 'Belum absen masuk';
                } else if (!userCoordinates) {
                    btnCheckOut.disabled = true;
                    btnCheckOut.className = 'py-3 px-3 rounded-2xl bg-slate-200 text-slate-400 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                    btnCheckOutSub.textContent = 'Periksa lokasi dahulu';
                } else if (!isInsideRadius) {
                    btnCheckOut.disabled = true;
                    btnCheckOut.className = 'py-3 px-3 rounded-2xl bg-rose-100/70 text-rose-500 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                    btnCheckOutSub.textContent = 'Di luar area presensi';
                } else {
                    btnCheckOut.disabled = false;
                    btnCheckOut.className = 'py-3 px-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-md shadow-indigo-500/25 transition cursor-pointer';
                    btnCheckOutSub.textContent = 'Kirim presensi pulang';
                }
            }

            // Periksa Lokasi Melalui Sensor Geolocation API
            btnCheckLocation.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    showGpsError('Browser Tidak Mendukung', 'Browser Anda tidak mendukung Geolocation API.');
                    return;
                }

                gpsStatusPill.textContent = 'Mengambil Lokasi...';
                gpsStatusPill.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200';
                btnCheckLocationText.textContent = 'Mendeteksi Satelit GPS...';
                btnGpsIcon.classList.add('animate-spin');
                gpsErrorBox.classList.add('hidden');

                const options = {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                };

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        btnGpsIcon.classList.remove('animate-spin');
                        btnCheckLocationText.textContent = 'Perbarui Lokasi Saya';
                        processCoordinates(
                            position.coords.latitude,
                            position.coords.longitude,
                            position.coords.accuracy
                        );
                    },
                    function (error) {
                        btnGpsIcon.classList.remove('animate-spin');
                        btnCheckLocationText.textContent = 'Coba Periksa Ulang';
                        gpsStatusPill.textContent = 'Gagal Mengambil Lokasi';
                        gpsStatusPill.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200';

                        let title = 'Gagal Mengambil Lokasi';
                        let message = 'Terjadi kendala sensor GPS pada browser.';

                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                title = 'Izin Lokasi Ditolak';
                                message = 'Harap izinkan akses lokasi (GPS) pada pengaturan browser Anda.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                title = 'Posisi Tidak Tersedia';
                                message = 'Sinyal satelit GPS tidak terdeteksi. Pastikan GPS HP aktif.';
                                break;
                            case error.TIMEOUT:
                                title = 'Waktu Permintaan Habis';
                                message = 'Sensor GPS terlalu lama merespons. Silakan coba kembali.';
                                break;
                        }

                        showGpsError(title, message);
                        userCoordinates = null;
                        areaStatusBadge.textContent = 'GPS Terkendala';
                        areaStatusBadge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200';
                        updateActionButtons();
                    },
                    options
                );
            });

            function showGpsError(title, message) {
                document.getElementById('gpsErrorTitle').textContent = title;
                gpsErrorMessage.textContent = message;
                gpsErrorBox.classList.remove('hidden');
            }

            // POST Presensi Masuk ke Database
            btnCheckIn.addEventListener('click', function () {
                if (!isInsideRadius || hasCheckedIn || !userCoordinates) return;

                btnCheckIn.disabled = true;
                btnCheckInSub.textContent = 'Menyimpan ke server...';

                fetch("{{ route('presensi.mobile.checkin') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        employee_id: currentEmployee.id,
                        latitude: userCoordinates.lat,
                        longitude: userCoordinates.lng,
                        notes: `Presensi masuk via Mobile GPS (${calculatedDistance}m)`
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        hasCheckedIn = true;
                        const timeIn = data.data.time_in ? data.data.time_in.substring(0, 5) : '--:--';

                        badgeIn.className = 'w-2 h-2 rounded-full bg-emerald-500';
                        textTimeIn.textContent = `${timeIn} WIB`;
                        textTimeIn.className = 'text-base font-bold text-emerald-600 font-mono-num';
                        descTimeIn.textContent = `Tercatat di area (${calculatedDistance}m)`;

                        overallAttendanceSummary.textContent = 'Hadir (Tercatat)';
                        overallAttendanceSummary.className = 'text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200';

                        showToast(`Presensi masuk berhasil dicatat pada ${timeIn} WIB.`);
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Gagal menyimpan presensi masuk.');
                        updateActionButtons();
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Gagal menghubungi server presensi.');
                    updateActionButtons();
                });
            });

            // POST Presensi Pulang ke Database
            btnCheckOut.addEventListener('click', function () {
                if (!isInsideRadius || !hasCheckedIn || hasCheckedOut || !userCoordinates) return;

                btnCheckOut.disabled = true;
                btnCheckOutSub.textContent = 'Menyimpan ke server...';

                fetch("{{ route('presensi.mobile.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        employee_id: currentEmployee.id,
                        latitude: userCoordinates.lat,
                        longitude: userCoordinates.lng
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        hasCheckedOut = true;
                        const timeOut = data.data.time_out ? data.data.time_out.substring(0, 5) : '--:--';

                        badgeOut.className = 'w-2 h-2 rounded-full bg-emerald-500';
                        textTimeOut.textContent = `${timeOut} WIB`;
                        textTimeOut.className = 'text-base font-bold text-emerald-600 font-mono-num';
                        descTimeOut.textContent = `Tercatat di area (${calculatedDistance}m)`;

                        overallAttendanceSummary.textContent = 'Presensi Selesai Lengkap';
                        overallAttendanceSummary.className = 'text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200';

                        showToast(`Presensi pulang berhasil dicatat pada ${timeOut} WIB.`);
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Gagal menyimpan presensi pulang.');
                        updateActionButtons();
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Gagal menghubungi server presensi.');
                    updateActionButtons();
                });
            });

            function showToast(msg) {
                toastMessage.textContent = msg;
                actionToast.classList.remove('hidden');
                setTimeout(() => actionToast.classList.add('hidden'), 5000);
            }

            // Inisialisasi awal tombol
            updateActionButtons();
        });
    </script>
</body>
</html>
