<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', 'YABAT PRESENSI | Portal Presensi Yayasan Anak Bangsa Aceh Tenggara')</title>

    <link rel="icon" type="image/webp" href="{{ asset('logo.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.webp') }}">
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full text-slate-800 antialiased bg-slate-50 flex overflow-hidden">
    
    <!-- Mobile Backdrop -->
    <div id="mobileBackdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden hidden transition-opacity duration-200"></div>

    <!-- Sidebar (Desktop Persistent) -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 flex flex-col justify-between transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:z-auto border-r border-slate-800/80 flex-shrink-0">
        <div class="flex flex-col h-full overflow-hidden">
            <!-- Sidebar Header / Brand -->
            <div class="h-14 flex items-center justify-between px-5 border-b border-slate-800/80 bg-slate-950/40">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('logo.webp') }}" alt="Logo YABAT" class="w-8 h-8 object-contain">
                    <div>
                        <div class="font-bold text-[13px] tracking-wider text-white">YABAT PRESENSI</div>
                        <div class="text-[10px] text-slate-400 font-medium">Admin Yayasan</div>
                    </div>
                </div>
                <!-- Close Button (Mobile Only) -->
                <button type="button" id="closeSidebarBtn" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none" aria-label="Tutup Menu">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-3.5 py-4 space-y-1">
                <div class="px-2.5 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    Menu Utama
                </div>

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'text-white bg-blue-600 shadow-sm shadow-blue-600/30' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Data Pegawai -->
                <a href="{{ route('admin.pegawai') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('admin.pegawai') ? 'text-white bg-blue-600 shadow-sm shadow-blue-600/30' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.pegawai') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Data Pegawai</span>
                </a>

                <!-- Institusi -->
                <a href="{{ route('admin.institusi') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('admin.institusi') ? 'text-white bg-blue-600 shadow-sm shadow-blue-600/30' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.institusi') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Institusi</span>
                </a>

                <!-- Jadwal Kerja -->
                <a href="{{ route('admin.jadwal') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('admin.jadwal') ? 'text-white bg-blue-600 shadow-sm shadow-blue-600/30' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.jadwal') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Jadwal Kerja</span>
                </a>

                <div class="pt-3 px-2.5 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    Aktivitas Presensi
                </div>

                <!-- Presensi -->
                <a href="{{ route('admin.presensi') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('admin.presensi') ? 'text-white bg-blue-600 shadow-sm shadow-blue-600/30' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.presensi') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>Presensi</span>
                </a>

                <!-- Riwayat Presensi -->
                <a href="{{ route('admin.riwayat') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('admin.riwayat') ? 'text-white bg-blue-600 shadow-sm shadow-blue-600/30' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.riwayat') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Riwayat Presensi</span>
                </a>

                <!-- Laporan -->
                <a href="{{ route('admin.laporan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('admin.laporan') ? 'text-white bg-blue-600 shadow-sm shadow-blue-600/30' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.laporan') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Laporan</span>
                </a>

                <div class="pt-3 px-2.5 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    Sistem
                </div>

                <!-- Pengaturan -->
                <a href="{{ route('admin.pengaturan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('admin.pengaturan') ? 'text-white bg-blue-600 shadow-sm shadow-blue-600/30' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.pengaturan') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Pengaturan</span>
                </a>
            </div>

            <!-- Sidebar Footer / Exit to Login -->
            <div class="p-3.5 border-t border-slate-800 bg-slate-950/20">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-slate-400 hover:text-rose-400 hover:bg-slate-800/80 transition group">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area (Clean Headerless) -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Mobile Trigger Bar (Hanya tampil di HP) -->
        <div class="lg:hidden px-4 py-3 bg-white border-b border-slate-200 flex items-center justify-between flex-shrink-0">
            <button type="button" id="openSidebarBtn" class="p-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none" aria-label="Buka Menu">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div class="text-xs font-bold text-slate-900 tracking-wider">YABAT PRESENSI</div>
            <div class="w-6"></div>
        </div>

        <!-- Main Body Content -->
        <main class="flex-1 overflow-y-auto px-6 py-7 lg:px-10 lg:py-8">
            @yield('content')
        </main>
    </div>

    <!-- Sidebar Drawer Script (Mobile Only) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const openBtn = document.getElementById('openSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');
            const backdrop = document.getElementById('mobileBackdrop');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (openBtn) openBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);
        });
    </script>
</body>
</html>
