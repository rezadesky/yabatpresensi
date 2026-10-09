@extends('layouts.mobile')

@section('title', 'Riwayat Presensi | YABAT Attendance')

@section('content')
<!-- ==========================================
     HALAMAN RIWAYAT PRESENSI MOBILE
     - Ringkasan Statistik Bulanan Pegawai
     - Daftar Log Presensi Terkini Lengkap
     - Detail Jam In/Out & Status Kehadiran
     ========================================== -->

<!-- 1. Ringkasan Presensi Pegawai Bulan Ini -->
<section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Rekapitulasi Pribadi</span>
            <div class="text-xs font-bold text-slate-800 mt-0.5">Bulan {{ \Carbon\Carbon::now()->isoFormat('MMMM Y') }}</div>
        </div>
        <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
            {{ $historyRecords->total() }} Log
        </span>
    </div>

    <!-- 4 Minimal Stat Cards -->
    <div class="grid grid-cols-4 gap-2 text-center">
        <div class="bg-emerald-50/70 border border-emerald-100 p-2 rounded-xl">
            <div class="text-[10px] font-semibold text-emerald-800">Hadir</div>
            <div class="text-sm font-bold text-emerald-700 font-mono-num mt-0.5">
                {{ $historyRecords->where('status', 'hadir')->count() }}
            </div>
        </div>
        <div class="bg-amber-50/70 border border-amber-100 p-2 rounded-xl">
            <div class="text-[10px] font-semibold text-amber-800">Telat</div>
            <div class="text-sm font-bold text-amber-700 font-mono-num mt-0.5">
                {{ $historyRecords->where('status', 'terlambat')->count() }}
            </div>
        </div>
        <div class="bg-blue-50/70 border border-blue-100 p-2 rounded-xl">
            <div class="text-[10px] font-semibold text-blue-800">Izin</div>
            <div class="text-sm font-bold text-blue-700 font-mono-num mt-0.5">
                {{ $historyRecords->whereIn('status', ['izin', 'sakit'])->count() }}
            </div>
        </div>
        <div class="bg-rose-50/70 border border-rose-100 p-2 rounded-xl">
            <div class="text-[10px] font-semibold text-rose-800">Alpa</div>
            <div class="text-sm font-bold text-rose-700 font-mono-num mt-0.5">
                {{ $historyRecords->where('status', 'alpa')->count() }}
            </div>
        </div>
    </div>
</section>

<!-- 2. Daftar Riwayat Presensi Komprehensif -->
<section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
    <div class="flex items-center justify-between pb-1 border-b border-slate-100">
        <div>
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Catatan Waktu</span>
            <div class="text-xs font-bold text-slate-800">Daftar Presensi Harian</div>
        </div>
        <span class="text-[10px] text-slate-400">Diurutkan terbaru</span>
    </div>

    <!-- Attendance Items List -->
    <div class="divide-y divide-slate-100">
        @forelse ($historyRecords as $item)
            <div class="py-3 space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-xs text-slate-800">
                        {{ \Carbon\Carbon::parse($item->date)->isoFormat('dddd, D MMMM Y') }}
                    </span>
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ $item->status === 'hadir' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($item->status === 'terlambat' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600') }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-600 bg-slate-50/70 p-2 rounded-lg border border-slate-100">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Waktu Masuk:</span>
                        <span class="font-mono-num font-semibold text-slate-800">
                            {{ $item->time_in ? \Carbon\Carbon::parse($item->time_in)->format('H:i:s') . ' WIB' : '--:--:--' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">Waktu Pulang:</span>
                        <span class="font-mono-num font-semibold text-slate-800">
                            {{ $item->time_out ? \Carbon\Carbon::parse($item->time_out)->format('H:i:s') . ' WIB' : 'Belum checkout' }}
                        </span>
                    </div>
                </div>

                <!-- Detail Lokasi GPS Koordinat Masuk -->
                @if ($item->latitude_in && $item->longitude_in)
                    <div class="flex items-center justify-between text-[10px] text-slate-400 px-0.5">
                        <span class="flex items-center gap-1 text-emerald-600 font-medium">
                            <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            GPS Masuk Valid
                        </span>
                        <span class="font-mono-num">{{ number_format($item->latitude_in, 5) }}, {{ number_format($item->longitude_in, 5) }}</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="py-8 text-center text-xs text-slate-400 space-y-2">
                <svg class="w-8 h-8 text-slate-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p>Belum ada catatan presensi tersimpan.</p>
            </div>
        @endforelse
    </div>

    <!-- Simple Pagination Link jika lebih dari 1 halaman -->
    @if ($historyRecords->hasPages())
        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
            {{ $historyRecords->links() }}
        </div>
    @endif
</section>
@endsection
