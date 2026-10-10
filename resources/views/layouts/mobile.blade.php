<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="YabatPresensi">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'YABAT Mobile | Portal Presensi Pegawai')</title>

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
    </script>

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

        /* Page Transition Animations */
        @keyframes pageFadeSlideUp {
            0% {
                opacity: 0;
                transform: translateY(8px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-enter-animate {
            animation: pageFadeSlideUp 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            will-change: opacity, transform;
        }

        /* Navigation Micro-animations */
        .nav-item-active {
            transform: translateY(-2px);
        }

        @keyframes navPulseDot {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.35);
                opacity: 0.8;
            }
        }

        .pulse-nav-dot {
            animation: navPulseDot 2s infinite ease-in-out;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full text-slate-800 antialiased bg-slate-100 selection:bg-blue-600 selection:text-white">

    <!-- Container Utama: Mobile First (Maksimal lebar layar HP / Tablet di tengah) -->
    <div class="min-h-full max-w-md mx-auto bg-slate-50 border-x border-slate-200/70 flex flex-col relative shadow-2xl pb-safe">

        <!-- ==========================================
             TOP HEADER PEGAWAI & IDENTITAS YABAT
             ========================================== -->
        <header class="bg-gradient-to-b from-slate-950 via-slate-900 to-slate-900 text-white px-5 pt-6 pb-5 rounded-b-3xl shadow-lg relative overflow-hidden flex-shrink-0">
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-blue-600/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-sky-500/15 rounded-full blur-xl pointer-events-none"></div>

            <div class="relative z-10 space-y-3.5">
                <!-- Brand Header -->
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="Logo YABAT" class="w-9 h-9 object-contain drop-shadow flex-shrink-0">
                    <div class="min-w-0">
                        <span class="text-sm font-extrabold tracking-wider text-white uppercase block leading-tight">YABAT PRESENSI</span>
                        <span class="text-[10px] text-blue-300 font-medium block leading-normal mt-0.5">Portal Presensi Yayasan Anak Bangsa Aceh Tenggara</span>
                    </div>
                </div>

                <!-- Identitas Pegawai yang Sedang Login (Terkunci Sesuai Akun Pribadi) -->
                <div class="bg-slate-800/70 border border-slate-700/60 backdrop-blur-sm rounded-2xl p-3.5 flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-blue-600/30 border border-blue-500/40 text-blue-300 font-extrabold text-sm flex items-center justify-center flex-shrink-0">
                            {{ strtoupper(substr($selectedEmployee->name ?? 'P', 0, 2)) }}
                        </div>
                        <div class="truncate">
                            <div class="font-bold text-white text-xs tracking-tight truncate">{{ $selectedEmployee->name ?? auth()->user()->name }}</div>
                            <div class="text-[11px] text-slate-300 truncate">{{ $selectedEmployee->position ?? 'Pegawai Yayasan' }}</div>
                            <div class="text-[10px] font-mono-num text-slate-400">NIP. {{ $selectedEmployee->nip_nidn ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 pl-2">
                        <div class="text-xs font-semibold text-blue-300 max-w-[130px] truncate leading-tight">{{ $selectedEmployee->institution->name ?? '-' }}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Yayasan Anak Bangsa</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT PAGE -->
        <main class="flex-1 px-4 py-4 space-y-4 page-enter-animate">
            @yield('content')
        </main>

        <!-- ==========================================
             BOTTOM NAVIGATION BAR MOBILE (4 TABS)
             ========================================== -->
        <nav class="fixed bottom-0 left-0 right-0 z-30 max-w-md mx-auto bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-3 py-2 shadow-lg">
            <div class="flex items-center justify-around text-[10px] font-semibold">
                
                <!-- 1. BERANDA -->
                <a href="{{ route('mobile.beranda') }}" 
                   class="flex flex-col items-center gap-1 py-1 px-3 transition-all duration-200 active:scale-95 {{ request()->routeIs('mobile.beranda') ? 'text-blue-600 font-bold nav-item-active' : 'text-slate-400 hover:text-slate-600' }}">
                    <div class="relative">
                        <svg class="w-5 h-5 transition-transform duration-200 {{ request()->routeIs('mobile.beranda') ? 'text-blue-600 scale-110 drop-shadow-sm' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('mobile.beranda') ? '2.5' : '2' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        @if(request()->routeIs('mobile.beranda'))
                            <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-blue-600 shadow-sm shadow-blue-400 pulse-nav-dot"></span>
                        @endif
                    </div>
                    <span>Beranda</span>
                </a>

                <!-- 2. PRESENSI -->
                <a href="{{ route('presensi.mobile') }}" 
                   class="flex flex-col items-center gap-1 py-1 px-3 transition-all duration-200 active:scale-95 {{ request()->routeIs('presensi.mobile') ? 'text-blue-600 font-bold nav-item-active' : 'text-slate-400 hover:text-slate-600' }}">
                    <div class="relative">
                        <svg class="w-5 h-5 transition-transform duration-200 {{ request()->routeIs('presensi.mobile') ? 'text-blue-600 scale-110 drop-shadow-sm' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('presensi.mobile') ? '2.5' : '2' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        @if(request()->routeIs('presensi.mobile'))
                            <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-blue-600 shadow-sm shadow-blue-400 pulse-nav-dot"></span>
                        @endif
                    </div>
                    <span>Presensi</span>
                </a>

                <!-- 3. RIWAYAT -->
                <a href="{{ route('mobile.riwayat') }}" 
                   class="flex flex-col items-center gap-1 py-1 px-3 transition-all duration-200 active:scale-95 {{ request()->routeIs('mobile.riwayat') ? 'text-blue-600 font-bold nav-item-active' : 'text-slate-400 hover:text-slate-600' }}">
                    <div class="relative">
                        <svg class="w-5 h-5 transition-transform duration-200 {{ request()->routeIs('mobile.riwayat') ? 'text-blue-600 scale-110 drop-shadow-sm' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('mobile.riwayat') ? '2.5' : '2' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @if(request()->routeIs('mobile.riwayat'))
                            <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-blue-600 shadow-sm shadow-blue-400 pulse-nav-dot"></span>
                        @endif
                    </div>
                    <span>Riwayat</span>
                </a>

                <!-- 4. PROFIL -->
                <a href="{{ route('mobile.profil') }}" 
                   class="flex flex-col items-center gap-1 py-1 px-3 transition-all duration-200 active:scale-95 {{ request()->routeIs('mobile.profil') ? 'text-blue-600 font-bold nav-item-active' : 'text-slate-400 hover:text-slate-600' }}">
                    <div class="relative">
                        <svg class="w-5 h-5 transition-transform duration-200 {{ request()->routeIs('mobile.profil') ? 'text-blue-600 scale-110 drop-shadow-sm' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('mobile.profil') ? '2.5' : '2' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        @if(request()->routeIs('mobile.profil'))
                            <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-blue-600 shadow-sm shadow-blue-400 pulse-nav-dot"></span>
                        @endif
                    </div>
                    <span>Profil</span>
                </a>

            </div>
        </nav>
    </div>

    @stack('scripts')
</body>
</html>
