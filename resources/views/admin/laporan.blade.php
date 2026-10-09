@extends('layouts.admin')

@section('title', 'Laporan Rekapitulasi Presensi | YABAT PRESENSI')

@section('content')
<div class="max-w-7xl mx-auto space-y-7 pb-12" x-data="{ period: '{{ $periodType }}' }">
    <!-- Header -->
    <div class="pb-2">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
            Laporan Presensi
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            Pilih unit institusi untuk menghasilkan rekapitulasi kehadiran resmi dan mencetak berkas laporan
        </p>
    </div>

    <!-- Filter Periode (Bulanan, Tahunan, Rentang Tanggal Bebas) -->
    <form method="GET" action="{{ route('admin.laporan') }}" class="bg-white p-4 rounded-xl border border-slate-200/80 space-y-3">
        @if ($institutionId)
            <input type="hidden" name="institution_id" value="{{ $institutionId }}">
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-700">Tipe Periode:</span>
                <div class="inline-flex rounded-lg bg-slate-100 p-0.5 text-xs">
                    <button 
                        type="button" 
                        @click="period = 'bulanan'"
                        :class="period === 'bulanan' ? 'bg-white shadow-sm font-semibold text-blue-600' : 'text-slate-500 hover:text-slate-800'"
                        class="px-3 py-1 rounded-md transition"
                    >
                        Per Bulan
                    </button>
                    <button 
                        type="button" 
                        @click="period = 'tahunan'"
                        :class="period === 'tahunan' ? 'bg-white shadow-sm font-semibold text-blue-600' : 'text-slate-500 hover:text-slate-800'"
                        class="px-3 py-1 rounded-md transition"
                    >
                        Per Tahun
                    </button>
                    <button 
                        type="button" 
                        @click="period = 'rentang'"
                        :class="period === 'rentang' ? 'bg-white shadow-sm font-semibold text-blue-600' : 'text-slate-500 hover:text-slate-800'"
                        class="px-3 py-1 rounded-md transition"
                    >
                        Rentang Bebas
                    </button>
                </div>
                <input type="hidden" name="period_type" :value="period">
            </div>

            <div class="text-xs font-mono text-slate-600 bg-slate-50 px-2.5 py-1 rounded-md border border-slate-100">
                Periode: <strong class="text-slate-900">{{ $periodLabel }}</strong>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
            <!-- Pilihan Bulanan -->
            <div x-show="period === 'bulanan'" class="flex items-center gap-2">
                <label class="text-slate-500">Bulan:</label>
                <select name="month" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->locale('id')->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
                <label class="text-slate-500">Tahun:</label>
                <select name="year" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
                    @for ($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Pilihan Tahunan -->
            <div x-show="period === 'tahunan'" x-cloak class="flex items-center gap-2">
                <label class="text-slate-500">Tahun Rekapitulasi:</label>
                <select name="year" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
                    @for ($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Pilihan Rentang Bebas -->
            <div x-show="period === 'rentang'" x-cloak class="flex items-center gap-2">
                <label class="text-slate-500">Mulai:</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
                <span class="text-slate-400">s/d</span>
                <label class="text-slate-500">Sampai:</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700">
            </div>

            <div class="flex items-center gap-2 ml-auto">
                <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    Terapkan Periode
                </button>
            </div>
        </div>
    </form>

    <!-- 1. Pilihan 3 Unit Institusi -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">
                Pilih Unit Institusi:
            </h2>
            <span class="text-xs text-slate-400">Klik unit untuk memuat laporan &amp; mencetak</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($unitSummary as $item)
            @php
                $isSelected = $selectedInstitution && $selectedInstitution->id === $item['institution']->id;
            @endphp
            <a 
                href="{{ route('admin.laporan', array_merge(request()->query(), ['institution_id' => $item['institution']->id])) }}"
                class="p-5 rounded-xl border transition-all flex flex-col justify-between block {{ $isSelected ? 'bg-blue-50/40 border-blue-600 ring-2 ring-blue-600/20 shadow-sm' : 'bg-white border-slate-200/80 hover:border-slate-300 hover:shadow-sm' }}"
            >
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $isSelected ? 'text-blue-700' : 'text-slate-400' }}">
                            {{ $item['institution']->category }}
                        </span>
                        @if ($isSelected)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 bg-blue-100/70 px-2 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Dipilih
                            </span>
                        @else
                            <span class="text-xs text-slate-400 font-medium">Pilih &rarr;</span>
                        @endif
                    </div>
                    <h3 class="text-base font-bold text-slate-900">{{ $item['institution']->name }}</h3>
                    <div class="text-xs text-slate-500 mt-0.5">{{ $item['total_employees'] }} Pegawai Terdaftar</div>

                    <!-- Mini bar rasio -->
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden flex mt-3.5 mb-3">
                        <div class="bg-blue-600 h-full" style="width: {{ $item['rate'] }}%"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-3 border-t {{ $isSelected ? 'border-blue-200/60' : 'border-slate-100' }}">
                    <span class="text-slate-500">Hadir: <strong class="text-slate-900">{{ $item['hadir'] }}</strong></span>
                    <span class="text-slate-500">Terlambat: <strong class="text-amber-600">{{ $item['terlambat'] }}</strong></span>
                    <span class="font-bold {{ $item['rate'] >= 90 ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $item['rate'] }}%
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- 2. Lembar Rekapitulasi Resmi & Tombol Cetak (Hanya Tampil Jika Institusi Dipilih) -->
    @if ($selectedInstitution)
    <div class="space-y-6 pt-1">
        <!-- Banner Lembaga Terpilih + 1 Tombol Cetak Tunggal -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Rekapitulasi Siap Cetak</div>
                <h2 class="text-xl font-bold text-slate-900 mt-0.5">{{ $selectedInstitution->name }}</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Periode: {{ $periodLabel }} &bull; Kampus: {{ $selectedInstitution->address }}
                </p>
            </div>
            <div>
                <a 
                    href="{{ route('admin.laporan.export', array_merge(request()->query(), ['institution_id' => $selectedInstitution->id])) }}"
                    class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Cetak Lembar Rekapitulasi (Excel)</span>
                </a>
            </div>
        </div>

        <!-- 4 Kartu Metrik Rekapitulasi Unit -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200/80">
                <div class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Tingkat Disiplin</div>
                <div class="mt-2 text-xl font-bold text-slate-900 tracking-tight">{{ $disciplineRate }}%</div>
                <div class="mt-1 text-[11px] text-emerald-600 font-medium">Hadir tepat waktu</div>
            </div>
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200/80">
                <div class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Total Kehadiran</div>
                <div class="mt-2 text-xl font-bold text-slate-900 tracking-tight">{{ $totalHadir }} Kali</div>
                <div class="mt-1 text-[11px] text-slate-400">Akumulasi seluruh staf</div>
            </div>
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200/80">
                <div class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Total Terlambat</div>
                <div class="mt-2 text-xl font-bold text-slate-900 tracking-tight text-amber-600">{{ $totalTerlambat }} Kali</div>
                <div class="mt-1 text-[11px] text-amber-600">Lewat batas toleransi</div>
            </div>
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200/80">
                <div class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Izin &amp; Cuti Resmi</div>
                <div class="mt-2 text-xl font-bold text-slate-900 tracking-tight">{{ $totalIzin }} Hari</div>
                <div class="mt-1 text-[11px] text-slate-400">Surat terkonfirmasi</div>
            </div>
        </div>

        <!-- Tabel Rekap Eksekutif Unit (Tanpa baris log harian, fokus rekapitulasi) -->
        <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Ringkasan Eksekutif Kehadiran Unit
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Rekapitulasi parameter kehadiran resmi {{ $selectedInstitution->name }}
                    </p>
                </div>
                <span class="text-xs font-mono text-slate-500 bg-slate-50 px-2.5 py-1 rounded-md border border-slate-100">
                    {{ $periodLabel }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="text-[11px] font-semibold text-slate-400 border-b border-slate-100 uppercase tracking-wider">
                            <th class="py-3 px-5">Parameter Laporan</th>
                            <th class="py-3 px-5 text-right">Nilai Rekap</th>
                            <th class="py-3 px-5 text-right">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-5 font-semibold text-slate-900">Total Catatan Presensi Masuk</td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-slate-900">{{ $totalRecords }} Catatan</td>
                            <td class="py-3.5 px-5 text-right font-mono text-slate-500">100%</td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-5 font-semibold text-slate-900">Kehadiran Tepat Waktu (Disiplin)</td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-emerald-600">{{ $totalHadir }} Kali</td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-emerald-600">{{ $disciplineRate }}%</td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-5 font-semibold text-slate-900">Keterlambatan Masuk Jam Kerja</td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-amber-600">{{ $totalTerlambat }} Kali</td>
                            <td class="py-3.5 px-5 text-right font-mono text-amber-600">{{ $totalRecords > 0 ? round(($totalTerlambat / $totalRecords) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-5 font-semibold text-slate-900">Izin Kedinasan &amp; Sakit Terverifikasi</td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-blue-600">{{ $totalIzin }} Hari</td>
                            <td class="py-3.5 px-5 text-right font-mono text-blue-600">{{ $totalRecords > 0 ? round(($totalIzin / $totalRecords) * 100, 1) : 0 }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rincian Bulanan (Khusus jika Periode Tahunan) -->
        @if ($periodType === 'tahunan' && !empty($monthlyBreakdown))
        <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Rincian Bulanan (Tahun {{ $selectedYear }})</h3>
                <span class="text-xs text-slate-400">12 Bulan Kalender</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="text-[11px] font-semibold text-slate-400 border-b border-slate-100 uppercase tracking-wider">
                            <th class="py-3 px-5">Bulan</th>
                            <th class="py-3 px-5 text-right">Total Presensi</th>
                            <th class="py-3 px-5 text-right">Hadir Tepat</th>
                            <th class="py-3 px-5 text-right">Terlambat</th>
                            <th class="py-3 px-5 text-right">Izin/Sakit</th>
                            <th class="py-3 px-5 text-right">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($monthlyBreakdown as $mb)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-5 font-semibold text-slate-900">{{ $mb['month_name'] }}</td>
                            <td class="py-3 px-5 text-right font-mono text-slate-600">{{ $mb['total'] }}</td>
                            <td class="py-3 px-5 text-right font-mono text-emerald-600 font-semibold">{{ $mb['hadir'] }}</td>
                            <td class="py-3 px-5 text-right font-mono text-amber-600">{{ $mb['terlambat'] }}</td>
                            <td class="py-3 px-5 text-right font-mono text-blue-600">{{ $mb['izin'] }}</td>
                            <td class="py-3 px-5 text-right font-mono font-bold {{ $mb['rate'] >= 90 ? 'text-emerald-600' : 'text-slate-600' }}">
                                {{ $mb['rate'] }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    @else
    <!-- Placeholder Pilih Institusi -->
    <div class="bg-white border border-slate-200/80 rounded-xl p-8 text-center space-y-2">
        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <h3 class="text-sm font-bold text-slate-900">Pilih Unit Institusi di Atas</h3>
        <p class="text-xs text-slate-400 max-w-md mx-auto">
            Klik salah satu kartu institusi di atas untuk memuat statistik rekapitulasi kehadiran resmi dan mencetak lembar laporannya.
        </p>
    </div>
    @endif
</div>
@endsection
