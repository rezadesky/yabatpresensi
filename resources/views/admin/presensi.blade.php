@extends('layouts.admin')

@section('title', 'Presensi Hari Ini | YABAT PRESENSI')

@section('content')
<div class="max-w-7xl mx-auto space-y-7 pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-2">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Presensi Hari Ini
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Monitoring kehadiran langsung pegawai dari seluruh unit ({{ date('d F Y') }})
            </p>
        </div>
        <div class="inline-flex items-center gap-2 self-start sm:self-auto px-3 py-1 rounded-full bg-slate-100 text-[11px] font-medium text-slate-600">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Real-time Active</span>
        </div>
    </div>

    <!-- 4 Minimal Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200/80">
            <div class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Hadir Tepat Waktu</div>
            <div class="mt-2 text-lg sm:text-xl font-semibold text-slate-900 tracking-tight">{{ $hadirCount }}</div>
            <div class="mt-1 text-[11px] text-emerald-600 font-medium">Terverifikasi sistem</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200/80">
            <div class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Terlambat</div>
            <div class="mt-2 text-lg sm:text-xl font-semibold text-slate-900 tracking-tight flex items-baseline gap-2">
                <span>{{ $terlambatCount }}</span>
            </div>
            <div class="mt-1 text-[11px] text-amber-600 font-medium">Lewat batas toleransi</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200/80">
            <div class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Izin &amp; Sakit</div>
            <div class="mt-2 text-lg sm:text-xl font-semibold text-slate-900 tracking-tight flex items-baseline gap-2">
                <span>{{ $izinCount }}</span>
            </div>
            <div class="mt-1 text-[11px] text-blue-600">Keterangan resmi</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200/80">
            <div class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Alpa / Tanpa Ket.</div>
            <div class="mt-2 text-lg sm:text-xl font-semibold text-slate-900 tracking-tight flex items-baseline gap-2">
                <span>{{ $alpaCount }}</span>
            </div>
            <div class="mt-1 text-[11px] text-rose-500 font-medium">Belum ada kabar</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('admin.presensi') }}" class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/80 flex items-center justify-between gap-3">
        <div class="text-xs font-semibold text-slate-700">Filter Presensi Hari Ini:</div>
        <div class="flex items-center gap-2 flex-wrap text-xs">
            <select name="institution_id" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-slate-50/60 border border-slate-200 rounded-lg text-slate-700">
                <option value="">Semua Institusi</option>
                @foreach ($institutions as $inst)
                    <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>
                        {{ $inst->name }}
                    </option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-slate-50/60 border border-slate-200 rounded-lg text-slate-700">
                <option value="">Semua Status</option>
                <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin / Sakit</option>
            </select>
            @if(request()->hasAny(['institution_id', 'status']))
                <a href="{{ route('admin.presensi') }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-600">Reset</a>
            @endif
        </div>
    </form>

    <!-- Presensi Records Table -->
    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-900">Log Kehadiran Langsung</h2>
            <span class="text-xs text-slate-400">Database Live</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-[11px] font-semibold text-slate-400 border-b border-slate-100 uppercase tracking-wider">
                        <th class="py-3 px-5">Pegawai</th>
                        <th class="py-3 px-5">Unit</th>
                        <th class="py-3 px-5">Jam Masuk</th>
                        <th class="py-3 px-5">Jam Pulang</th>
                        <th class="py-3 px-5">Catatan / Keterangan</th>
                        <th class="py-3 px-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($attendances as $att)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-5 font-semibold text-slate-900">
                            <div>{{ $att->employee->name ?? '-' }}</div>
                            <div class="text-[11px] text-slate-400 font-normal">NIP: {{ $att->employee->nip_nidn ?? '-' }}</div>
                        </td>
                        <td class="py-3.5 px-5 text-slate-500">{{ $att->institution->name ?? '-' }}</td>
                        <td class="py-3.5 px-5 font-mono text-slate-700">
                            {{ $att->time_in ? date('H:i:s', strtotime($att->time_in)) . ' WIB' : '-' }}
                        </td>
                        <td class="py-3.5 px-5 font-mono text-slate-400">
                            {{ $att->time_out ? date('H:i:s', strtotime($att->time_out)) . ' WIB' : 'Belum Pulang' }}
                        </td>
                        <td class="py-3.5 px-5 text-slate-500">{{ $att->notes ?? 'Presensi terverifikasi' }}</td>
                        <td class="py-3.5 px-5 text-right">
                            @if ($att->status === 'hadir')
                                <span class="text-emerald-600 font-medium">Hadir</span>
                            @elseif ($att->status === 'terlambat')
                                <span class="text-amber-600 font-medium">Terlambat</span>
                            @elseif ($att->status === 'izin')
                                <span class="text-blue-600 font-medium">Izin</span>
                            @elseif ($att->status === 'sakit')
                                <span class="text-purple-600 font-medium">Sakit</span>
                            @else
                                <span class="text-rose-500 font-medium">Alpa</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">Belum ada catatan presensi hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($attendances->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
