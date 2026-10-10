<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Gunakan Aplikasi Mobile Resmi | YABAT PRESENSI</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-50 flex items-center justify-center p-4 text-slate-800 antialiased">
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xl text-center space-y-5">
        <div class="w-20 h-20 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center mx-auto shadow-sm">
            <img src="{{ asset('logo.png') }}" alt="Logo YABAT" class="w-12 h-12 object-contain">
        </div>

        <div class="space-y-1.5">
            <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                Akses Browser Dinonaktifkan
            </span>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Gunakan Aplikasi Mobile
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed px-2">
                Untuk menjamin akurasi dan keabsahan titik koordinat GPS, presensi pegawai <strong>hanya dapat dilakukan melalui Aplikasi Android Resmi YABAT PRESENSI</strong>.
            </p>
        </div>

        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 text-left space-y-2.5 text-xs">
            <div class="flex items-center gap-2.5 font-bold text-slate-800">
                <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>Panduan Presensi Pegawai:</span>
            </div>
            <ul class="list-disc list-inside text-slate-600 space-y-1 leading-relaxed text-[11px] pl-1">
                <li>Buka aplikasi <strong>YABAT PRESENSI</strong> di smartphone Anda.</li>
                <li>Masuk menggunakan Email/NIP dan kata sandi Anda.</li>
                <li>Pastikan GPS/Lokasi perangkat telah aktif saat presensi.</li>
            </ul>
        </div>

        <div class="pt-2 border-t border-slate-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 text-xs font-bold transition">
                    Kembali ke Halaman Login Admin
                </button>
            </form>
        </div>
    </div>
</body>
</html>
