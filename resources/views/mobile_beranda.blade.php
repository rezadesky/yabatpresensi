@extends('layouts.mobile')

@section('title', 'Beranda Pegawai | YABAT PRESENSI')

@section('content')
<!-- ==========================================
     HALAMAN BERANDA MOBILE
     - Ringkasan Status Kehadiran
     - Jam Digital & Jadwal Kerja
     - Informasi & Pengumuman Terbaru
     * Bebas tombol atau formulir presensi *
     ========================================== -->

<!-- 1. Hari, Tanggal & Real-Time Clock -->
<section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Waktu Operasional</span>
            <div id="berandaDate" class="text-xs font-bold text-slate-800 mt-0.5">Memuat hari & tanggal...</div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
            <span>Live WIB</span>
        </span>
    </div>

    <div class="text-center py-1 bg-slate-50/70 rounded-xl border border-slate-100">
        <div id="berandaClock" class="text-3xl font-extrabold text-slate-900 tracking-tight font-mono-num">
            --:--:--
        </div>
        <div class="text-[10px] text-slate-400 mt-0.5">Waktu Indonesia Barat (Kutacane, Aceh Tenggara)</div>
    </div>
</section>

<!-- 2. Ringkasan Status Kehadiran Hari Ini -->
<section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
    <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700">Status Kehadiran Hari Ini</span>
        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ $todayAttendance ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
            {{ $todayAttendance ? ucfirst($todayAttendance->status) : 'Belum Presensi' }}
        </span>
    </div>

    <!-- 2 Metric Cards: Jam Masuk & Jam Pulang -->
    <div class="grid grid-cols-2 gap-2.5">
        <div class="p-3 rounded-xl border {{ ($todayAttendance && $todayAttendance->time_in) ? 'bg-emerald-50/40 border-emerald-200/70' : 'bg-slate-50/50 border-slate-200/80' }} space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium text-slate-500">Presensi Masuk</span>
                <span class="w-2 h-2 rounded-full {{ ($todayAttendance && $todayAttendance->time_in) ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
            </div>
            <div class="text-base font-bold {{ ($todayAttendance && $todayAttendance->time_in) ? 'text-emerald-600' : 'text-slate-700' }} font-mono-num">
                {{ ($todayAttendance && $todayAttendance->time_in) ? \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i') . ' WIB' : '--:--' }}
            </div>
            <div class="text-[10px] text-slate-400">
                {{ ($todayAttendance && $todayAttendance->time_in) ? 'Tercatat valid di sistem' : 'Belum tercatat' }}
            </div>
        </div>

        <div class="p-3 rounded-xl border {{ ($todayAttendance && $todayAttendance->time_out) ? 'bg-emerald-50/40 border-emerald-200/70' : 'bg-slate-50/50 border-slate-200/80' }} space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium text-slate-500">Presensi Pulang</span>
                <span class="w-2 h-2 rounded-full {{ ($todayAttendance && $todayAttendance->time_out) ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
            </div>
            <div class="text-base font-bold {{ ($todayAttendance && $todayAttendance->time_out) ? 'text-emerald-600' : 'text-slate-700' }} font-mono-num">
                {{ ($todayAttendance && $todayAttendance->time_out) ? \Carbon\Carbon::parse($todayAttendance->time_out)->format('H:i') . ' WIB' : '--:--' }}
            </div>
            <div class="text-[10px] text-slate-400">
                {{ ($todayAttendance && $todayAttendance->time_out) ? 'Telah checkout sore' : 'Tersedia jam pulang' }}
            </div>
        </div>
    </div>

    <!-- Quick Link CTA ke Tab Presensi -->
    <div class="pt-1">
        <a href="{{ route('presensi.mobile') }}" 
           class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white text-xs font-semibold flex items-center justify-center gap-2 shadow-sm transition">
            <svg class="w-4 h-4 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Buka Halaman Presensi GPS</span>
        </a>
    </div>
</section>

<!-- 3. Jadwal Kerja Resmi Unit -->
<section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Jadwal Tugas Resmi</span>
            <div class="text-xs font-bold text-slate-800 mt-0.5">{{ $workSchedule->name ?? 'Jadwal Kerja Reguler' }}</div>
        </div>
        <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
            {{ $workSchedule->day_of_week ?? 'Senin - Sabtu' }}
        </span>
    </div>

    <div class="grid grid-cols-2 gap-2 text-xs">
        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
            <div class="text-[10px] text-slate-400 font-medium">Jam Masuk Kerja</div>
            <div class="text-sm font-bold text-slate-900 font-mono-num mt-0.5">
                {{ $workSchedule ? \Carbon\Carbon::parse($workSchedule->time_in)->format('H:i') . ' WIB' : '07:30 WIB' }}
            </div>
            <div class="text-[10px] text-slate-400">Toleransi: {{ $workSchedule->late_tolerance_minutes ?? 15 }} menit</div>
        </div>
        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
            <div class="text-[10px] text-slate-400 font-medium">Jam Pulang Kerja</div>
            <div class="text-sm font-bold text-slate-900 font-mono-num mt-0.5">
                {{ $workSchedule ? \Carbon\Carbon::parse($workSchedule->time_out)->format('H:i') . ' WIB' : '16:00 WIB' }}
            </div>
            <div class="text-[10px] text-slate-400">Presensi sebelum batas alpa</div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function updateBerandaClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const clockEl = document.getElementById('berandaClock');
            if (clockEl) clockEl.textContent = `${hours}:${minutes}:${seconds}`;

            const dateEl = document.getElementById('berandaDate');
            if (dateEl) {
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                dateEl.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
            }
        }
        setInterval(updateBerandaClock, 1000);
        updateBerandaClock();
    });
</script>
@endpush
