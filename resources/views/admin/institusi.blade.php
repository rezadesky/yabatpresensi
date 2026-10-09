@extends('layouts.admin')

@section('title', 'Institusi | YABAT PRESENSI')

@section('content')
<div class="max-w-7xl mx-auto space-y-7 pb-12">
    <!-- Header (Read-Only) -->
    <div class="pb-2">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
            Unit Institusi
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            Lembaga pendidikan naungan Yayasan Anak Bangsa
        </p>
    </div>

    <!-- 3 Fixed Clean Institution Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse ($institutions as $inst)
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 hover:border-slate-300 transition-colors flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider">{{ $inst->category }}</span>
                    <span class="text-emerald-600 text-xs font-medium">Aktif</span>
                </div>
                <h2 class="text-base font-bold text-slate-900">{{ $inst->name }}</h2>
                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                    {{ $inst->description ?? 'Lembaga pendidikan formal di bawah naungan Yayasan Anak Bangsa.' }}
                </p>

                <div class="mt-5 space-y-2 text-xs border-t border-slate-100 pt-3 text-slate-600">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kode Unit:</span>
                        <span class="font-mono text-slate-900 font-semibold">{{ $inst->code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Total Staf Terdaftar:</span>
                        <span class="font-semibold text-slate-900">{{ $inst->employees_count }} Pegawai</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Radius Presensi GPS:</span>
                        <span class="font-mono text-slate-700 font-medium">{{ $inst->radius_meters }} Meter</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Pimpinan / Kepala:</span>
                        <span class="text-slate-700 font-medium">{{ $inst->head_name ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-1 pt-1 border-t border-slate-50">
                        <span class="text-slate-400">Alamat Kampus:</span>
                        <span class="text-slate-700 font-medium sm:text-right leading-tight" title="{{ $inst->address }}">{{ $inst->address ?? 'Pulonas Baru, Kec. Lawe Bulan, Kabupaten Aceh Tenggara, Aceh' }}</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-white p-8 rounded-xl border text-center text-slate-400">
            Tidak ada unit institusi terdaftar.
        </div>
        @endforelse
    </div>
</div>
@endsection
