@extends('layouts.admin')

@section('title', 'Riwayat Presensi | YABAT PRESENSI')

@section('content')
<div class="max-w-7xl mx-auto space-y-7 pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-2">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Riwayat Presensi
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Arsip log kehadiran lampau pegawai seluruh institusi YABAT
            </p>
        </div>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition self-start sm:self-auto">
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak / Ekspor Log</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('admin.riwayat') }}" class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-center gap-2 flex-wrap text-xs flex-1">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Cari nama pegawai / NIP..." 
                class="px-2.5 py-1.5 rounded-lg bg-slate-50/60 border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600"
            >
            <span class="text-slate-400 font-medium">Rentang:</span>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-2.5 py-1.5 rounded-lg bg-slate-50/60 border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600">
            <span class="text-slate-400">-</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-2.5 py-1.5 rounded-lg bg-slate-50/60 border border-slate-200 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600">
            
            <select name="institution_id" class="px-2.5 py-1.5 bg-slate-50/60 border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600">
                <option value="">Semua Unit</option>
                @foreach ($institutions as $inst)
                    <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>
                        {{ $inst->name }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="px-2.5 py-1.5 bg-slate-50/60 border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600">
                <option value="">Semua Status</option>
                <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin / Sakit</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                Terapkan Filter
            </button>
            @if(request()->hasAny(['search', 'start_date', 'end_date', 'institution_id', 'status']))
                <a href="{{ route('admin.riwayat') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-medium">Reset</a>
            @endif
        </div>
    </form>

    <!-- Log Table -->
    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-[11px] font-semibold text-slate-400 border-b border-slate-100 uppercase tracking-wider">
                        <th class="py-3 px-5">Tanggal</th>
                        <th class="py-3 px-5">Pegawai</th>
                        <th class="py-3 px-5">Unit</th>
                        <th class="py-3 px-5">Jam Masuk</th>
                        <th class="py-3 px-5">Jam Pulang</th>
                        <th class="py-3 px-5">Keterangan</th>
                        <th class="py-3 px-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($records as $rec)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-5 font-mono text-slate-500 whitespace-nowrap">{{ date('d/m/Y', strtotime($rec->date)) }}</td>
                        <td class="py-3.5 px-5 font-semibold text-slate-900">
                            <div>{{ $rec->employee->name ?? '-' }}</div>
                            <div class="text-[11px] text-slate-400 font-normal">NIP: {{ $rec->employee->nip_nidn ?? '-' }}</div>
                        </td>
                        <td class="py-3.5 px-5 text-slate-500">{{ $rec->institution->name ?? '-' }}</td>
                        <td class="py-3.5 px-5 font-mono text-slate-700">{{ $rec->time_in ? date('H:i:s', strtotime($rec->time_in)) . ' WIB' : '-' }}</td>
                        <td class="py-3.5 px-5 font-mono text-slate-400">{{ $rec->time_out ? date('H:i:s', strtotime($rec->time_out)) . ' WIB' : '-' }}</td>
                        <td class="py-3.5 px-5 text-slate-500">{{ $rec->notes ?? '-' }}</td>
                        <td class="py-3.5 px-5 text-right">
                            @if ($rec->status === 'hadir')
                                <span class="text-emerald-600 font-medium">Hadir</span>
                            @elseif ($rec->status === 'terlambat')
                                <span class="text-amber-600 font-medium">Terlambat</span>
                            @elseif ($rec->status === 'izin')
                                <span class="text-blue-600 font-medium">Izin</span>
                            @elseif ($rec->status === 'sakit')
                                <span class="text-purple-600 font-medium">Sakit</span>
                            @else
                                <span class="text-rose-500 font-medium">Alpa</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada riwayat presensi yang sesuai kriteria pencarian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($records->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
