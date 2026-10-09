@extends('layouts.admin')

@section('title', 'Jadwal Kerja | YABAT PRESENSI')

@section('content')
<div class="max-w-7xl mx-auto space-y-7 pb-12" x-data="{ openCreateModal: false, openEditModal: false, editData: {} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-2">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Jadwal Kerja
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Pengaturan shift kerja dan toleransi kehadiran pegawai
            </p>
        </div>
        <button 
            type="button" 
            @click="openCreateModal = true"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition self-start sm:self-auto"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Shift</span>
        </button>
    </div>

    @if (session('success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
            <div class="font-bold mb-1">Gagal Menyimpan Jadwal:</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Shifts Table -->
    <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-900">Shift Kerja Terdaftar</h2>
            <span class="text-xs text-slate-400">{{ count($schedules) }} Pola Waktu</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-[11px] font-semibold text-slate-400 border-b border-slate-100 uppercase tracking-wider">
                        <th class="py-3 px-5">Shift</th>
                        <th class="py-3 px-5">Unit Terkait</th>
                        <th class="py-3 px-5">Jam Masuk</th>
                        <th class="py-3 px-5">Toleransi</th>
                        <th class="py-3 px-5">Jam Pulang</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($schedules as $sch)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-5 font-semibold text-slate-900">
                            <div>{{ $sch->name }}</div>
                            <div class="text-[11px] text-slate-400 font-normal">{{ $sch->day_of_week }}</div>
                        </td>
                        <td class="py-3.5 px-5 text-slate-500">{{ $sch->institution ? $sch->institution->name : 'Semua Unit Yayasan' }}</td>
                        <td class="py-3.5 px-5 font-mono text-slate-800 font-semibold">{{ date('H:i', strtotime($sch->time_in)) }} WIB</td>
                        <td class="py-3.5 px-5 text-amber-600 font-medium">{{ $sch->late_tolerance_minutes }} Menit</td>
                        <td class="py-3.5 px-5 font-mono text-slate-800">{{ date('H:i', strtotime($sch->time_out)) }} WIB</td>
                        <td class="py-3.5 px-5 text-right">
                            <div class="inline-flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="editData = {{ json_encode($sch) }}; openEditModal = true"
                                    class="text-blue-600 hover:text-blue-800 font-medium"
                                >
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.jadwal.destroy', $sch->id) }}" onsubmit="return confirm('Hapus shift {{ $sch->name }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-700 font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400">Belum ada jadwal kerja tersimpan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Shift -->
    <div x-show="openCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto" @click.away="openCreateModal = false">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-slate-900">Tambah Shift Kerja Baru</h3>
                <button type="button" @click="openCreateModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.jadwal.store') }}" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Shift *</label>
                    <input type="text" name="name" required placeholder="Contoh: Reguler STKIP / Asrama" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Unit Institusi</label>
                    <select name="institution_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-600">
                        <option value="">Semua Unit Yayasan</option>
                        @foreach ($institutions as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Hari Kerja *</label>
                    <input type="text" name="day_of_week" required placeholder="Senin - Sabtu / Setiap Hari" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jam Masuk *</label>
                        <input type="time" name="time_in" required value="07:30" class="w-full px-3 py-2 border rounded-lg font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jam Pulang *</label>
                        <input type="time" name="time_out" required value="16:00" class="w-full px-3 py-2 border rounded-lg font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Toleransi (Mnt) *</label>
                        <input type="number" name="late_tolerance_minutes" required value="15" class="w-full px-3 py-2 border rounded-lg font-mono">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2 border rounded-lg text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">Simpan Shift</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Shift -->
    <div x-show="openEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto" @click.away="openEditModal = false">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-slate-900">Ubah Shift Kerja</h3>
                <button type="button" @click="openEditModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            <form :action="'{{ url('/admin/jadwal') }}/' + editData.id" method="POST" class="space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Shift *</label>
                    <input type="text" name="name" x-model="editData.name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Unit Institusi</label>
                    <select name="institution_id" x-model="editData.institution_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-600">
                        <option value="">Semua Unit Yayasan</option>
                        @foreach ($institutions as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Hari Kerja *</label>
                    <input type="text" name="day_of_week" x-model="editData.day_of_week" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jam Masuk *</label>
                        <input type="time" name="time_in" x-model="editData.time_in" required class="w-full px-3 py-2 border rounded-lg font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jam Pulang *</label>
                        <input type="time" name="time_out" x-model="editData.time_out" required class="w-full px-3 py-2 border rounded-lg font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Toleransi (Mnt) *</label>
                        <input type="number" name="late_tolerance_minutes" x-model="editData.late_tolerance_minutes" required class="w-full px-3 py-2 border rounded-lg font-mono">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 border rounded-lg text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
