@extends('layouts.mobile')

@section('title', 'Presensi GPS Pegawai | YABAT PRESENSI')

@section('content')
<!-- ==========================================
     HALAMAN PRESENSI MOBILE
     - Sensor Lokasi GPS (Browser Geolocation API)
     - Validasi Area Presensi (Geofencing Radius)
     - Tombol Langsung Presensi Masuk & Pulang
     - Status Presensi Hari Ini
     ========================================== -->

<!-- 1. Jam Digital & Status Kehadiran Hari Ini -->
<section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
    <div class="flex items-center justify-between">
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Status Kehadiran Hari Ini</span>
        <span id="overallAttendanceSummary" class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ $todayAttendance ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
            {{ $todayAttendance ? ucfirst($todayAttendance->status) . ' (Tercatat)' : 'Belum Presensi' }}
        </span>
    </div>

    <!-- Clock Display -->
    <div class="text-center py-1 bg-slate-50/70 rounded-xl border border-slate-100">
        <div id="realtimeClock" class="text-3xl font-extrabold text-slate-900 tracking-tight font-mono-num">
            --:--:--
        </div>
        <div class="text-[10px] text-slate-400 mt-0.5">Waktu Indonesia Barat (WIB)</div>
    </div>

    <!-- Status In / Out Hari Ini -->
    <div class="grid grid-cols-2 gap-2.5">
        <div class="p-3 rounded-xl border bg-slate-50/50 border-slate-200/80 space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium text-slate-500">Presensi Masuk</span>
                <span id="badgeIn" class="w-2 h-2 rounded-full {{ ($todayAttendance && $todayAttendance->time_in) ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
            </div>
            <div id="textTimeIn" class="text-base font-bold {{ ($todayAttendance && $todayAttendance->time_in) ? 'text-emerald-600' : 'text-slate-700' }} font-mono-num">
                {{ ($todayAttendance && $todayAttendance->time_in) ? \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i') . ' WIB' : '--:--' }}
            </div>
            <div id="descTimeIn" class="text-[10px] text-slate-400">
                {{ ($todayAttendance && $todayAttendance->time_in) ? 'Terverifikasi GPS' : 'Menunggu verifikasi GPS' }}
            </div>
        </div>

        <div class="p-3 rounded-xl border bg-slate-50/50 border-slate-200/80 space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-medium text-slate-500">Presensi Pulang</span>
                <span id="badgeOut" class="w-2 h-2 rounded-full {{ ($todayAttendance && $todayAttendance->time_out) ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
            </div>
            <div id="textTimeOut" class="text-base font-bold {{ ($todayAttendance && $todayAttendance->time_out) ? 'text-emerald-600' : 'text-slate-700' }} font-mono-num">
                {{ ($todayAttendance && $todayAttendance->time_out) ? \Carbon\Carbon::parse($todayAttendance->time_out)->format('H:i') . ' WIB' : '--:--' }}
            </div>
            <div id="descTimeOut" class="text-[10px] text-slate-400">
                {{ ($todayAttendance && $todayAttendance->time_out) ? 'Telah presensi pulang' : 'Tersedia jam pulang' }}
            </div>
        </div>
    </div>
</section>

<!-- 2. Sensor Lokasi GPS (Browser Geolocation API) -->
<section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3.5">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xs font-bold text-slate-900 leading-tight">Sensor Lokasi GPS</h2>
                <p class="text-[10px] text-slate-400">HTML5 Geolocation API</p>
            </div>
        </div>

        <!-- GPS Status Pill -->
        <span id="gpsStatusPill" class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
            Belum Diperiksa
        </span>
    </div>

    <!-- Koordinat & Akurasi Display -->
    <div class="bg-slate-50 rounded-xl p-3 border border-slate-200/70 text-xs space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-slate-400 text-[11px]">Latitude:</span>
            <span id="displayLat" class="font-mono-num font-semibold text-slate-800 text-[11px]">-</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-slate-400 text-[11px]">Longitude:</span>
            <span id="displayLng" class="font-mono-num font-semibold text-slate-800 text-[11px]">-</span>
        </div>
        <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
            <span class="text-slate-400 text-[11px]">Akurasi Sinyal GPS:</span>
            <span id="displayAccuracy" class="font-mono-num text-[11px] font-semibold text-slate-600">-</span>
        </div>
    </div>

    <!-- Error Notice Box (Hidden by default) -->
    <div id="gpsErrorBox" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
        <div class="font-bold flex items-center gap-1.5 text-rose-800">
            <svg class="w-4 h-4 flex-shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span id="gpsErrorTitle">Gagal Mengambil Lokasi</span>
        </div>
        <p id="gpsErrorMessage" class="text-[11px] text-rose-600 leading-relaxed">
            Izin lokasi ditolak atau sinyal GPS tidak terdeteksi. Silakan aktifkan GPS perangkat Anda.
        </p>
    </div>

    <!-- Tombol Periksa Lokasi GPS Perangkat -->
    <button 
        type="button" 
        id="btnCheckLocation" 
        class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white text-xs font-semibold flex items-center justify-center gap-2 shadow-sm transition">
        <svg id="btnGpsIcon" class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 16v-4m8-4h-4M4 12h4" />
        </svg>
        <span id="btnCheckLocationText">Periksa Lokasi Saya Sekarang</span>
    </button>
</section>

<!-- 3. Validasi Area Presensi (Geofencing) -->
<section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Verifikasi Area Kerja</span>
            <div id="targetInstitutionTitle" class="text-xs font-bold text-slate-800 mt-0.5">{{ $selectedEmployee->institution->name ?? 'Unit Institusi' }}</div>
        </div>
        <span id="areaStatusBadge" class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200">
            Menunggu GPS
        </span>
    </div>

    <div class="grid grid-cols-2 gap-2 text-xs">
        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
            <div class="text-[10px] text-slate-400 font-medium">Jarak ke Unit Kerja</div>
            <div id="displayDistance" class="text-sm font-bold text-slate-900 font-mono-num mt-0.5">-</div>
            <div class="text-[10px] text-slate-400">Dihitung otomatis (Haversine)</div>
        </div>
        <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
            <div class="text-[10px] text-slate-400 font-medium">Batas Maksimal Radius</div>
            <div id="displayAllowedRadius" class="text-sm font-bold text-blue-600 font-mono-num mt-0.5">{{ $selectedEmployee->institution->radius_meters ?? 100 }} Meter</div>
            <div class="text-[10px] text-slate-400">Kebijakan resmi yayasan</div>
        </div>
    </div>
</section>

<!-- 4. Tombol Presensi Langsung (Terkoneksi Database) -->
<section class="space-y-2 pt-1">
    <div class="grid grid-cols-2 gap-3">
        <!-- Tombol Masuk -->
        <button 
            type="button" 
            id="btnCheckIn" 
            disabled 
            class="py-3 px-3 rounded-2xl bg-slate-300 text-slate-500 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed">
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                <span>Presensi Masuk</span>
            </div>
            <span id="btnCheckInSub" class="text-[9px] font-normal opacity-80">
                {{ ($todayAttendance && $todayAttendance->time_in) ? 'Sudah presensi masuk' : 'Perlu periksa lokasi' }}
            </span>
        </button>

        <!-- Tombol Pulang -->
        <button 
            type="button" 
            id="btnCheckOut" 
            disabled 
            class="py-3 px-3 rounded-2xl bg-slate-300 text-slate-500 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed">
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Presensi Pulang</span>
            </div>
            <span id="btnCheckOutSub" class="text-[9px] font-normal opacity-80">
                {{ ($todayAttendance && $todayAttendance->time_out) ? 'Sudah presensi pulang' : 'Menunggu absen masuk' }}
            </span>
        </button>
    </div>

    <!-- Toast Interaktif -->
    <div id="actionToast" class="hidden p-3 rounded-xl bg-slate-900 text-white text-xs text-center font-medium shadow-lg animate-fade">
        <span id="toastMessage">Memproses presensi...</span>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const employeeId = {{ $selectedEmployee->id }};
        const targetLat = {{ $selectedEmployee->institution->latitude ?? 3.4883 }};
        const targetLng = {{ $selectedEmployee->institution->longitude ?? 97.8085 }};
        const targetRadius = {{ $selectedEmployee->institution->radius_meters ?? 100 }};

        let userCoordinates = null;
        let calculatedDistance = null;
        let isInsideRadius = false;
        let hasCheckedIn = {{ ($todayAttendance && $todayAttendance->time_in) ? 'true' : 'false' }};
        let hasCheckedOut = {{ ($todayAttendance && $todayAttendance->time_out) ? 'true' : 'false' }};

        const realtimeClock = document.getElementById('realtimeClock');
        const gpsStatusPill = document.getElementById('gpsStatusPill');
        const displayLat = document.getElementById('displayLat');
        const displayLng = document.getElementById('displayLng');
        const displayAccuracy = document.getElementById('displayAccuracy');
        const gpsErrorBox = document.getElementById('gpsErrorBox');
        const gpsErrorMessage = document.getElementById('gpsErrorMessage');
        const btnCheckLocation = document.getElementById('btnCheckLocation');
        const btnCheckLocationText = document.getElementById('btnCheckLocationText');
        const btnGpsIcon = document.getElementById('btnGpsIcon');

        const areaStatusBadge = document.getElementById('areaStatusBadge');
        const displayDistance = document.getElementById('displayDistance');

        const btnCheckIn = document.getElementById('btnCheckIn');
        const btnCheckInSub = document.getElementById('btnCheckInSub');
        const btnCheckOut = document.getElementById('btnCheckOut');
        const btnCheckOutSub = document.getElementById('btnCheckOutSub');
        const actionToast = document.getElementById('actionToast');
        const toastMessage = document.getElementById('toastMessage');

        const overallAttendanceSummary = document.getElementById('overallAttendanceSummary');
        const badgeIn = document.getElementById('badgeIn');
        const textTimeIn = document.getElementById('textTimeIn');
        const descTimeIn = document.getElementById('descTimeIn');
        const badgeOut = document.getElementById('badgeOut');
        const textTimeOut = document.getElementById('textTimeOut');
        const descTimeOut = document.getElementById('descTimeOut');

        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            if (realtimeClock) realtimeClock.textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
            const rad = Math.PI / 180;
            const phi1 = lat1 * rad;
            const phi2 = lat2 * rad;
            const deltaPhi = (lat2 - lat1) * rad;
            const deltaLambda = (lon2 - lon1) * rad;

            const a = Math.sin(deltaPhi / 2) * Math.sin(deltaPhi / 2) +
                      Math.cos(phi1) * Math.cos(phi2) *
                      Math.sin(deltaLambda / 2) * Math.sin(deltaLambda / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return Math.round(R * c);
        }

        function processCoordinates(lat, lng, accuracy) {
            userCoordinates = { lat, lng, accuracy };

            displayLat.textContent = lat.toFixed(6);
            displayLng.textContent = lng.toFixed(6);
            displayAccuracy.textContent = `± ${Math.round(accuracy)} Meter`;

            gpsErrorBox.classList.add('hidden');
            gpsStatusPill.textContent = 'Lokasi Ditemukan';
            gpsStatusPill.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200';

            calculatedDistance = calculateHaversineDistance(lat, lng, targetLat, targetLng);
            displayDistance.textContent = `${calculatedDistance.toLocaleString()} Meter`;

            if (calculatedDistance <= targetRadius) {
                isInsideRadius = true;
                areaStatusBadge.textContent = 'Dalam Area Presensi';
                areaStatusBadge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200';
            } else {
                isInsideRadius = false;
                areaStatusBadge.textContent = 'Di Luar Area Presensi';
                areaStatusBadge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200';
            }

            updateActionButtons();
        }

        function updateActionButtons() {
            if (hasCheckedIn) {
                btnCheckIn.disabled = true;
                btnCheckIn.className = 'py-3 px-3 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                btnCheckInSub.textContent = 'Sudah presensi masuk';
            } else if (!userCoordinates) {
                btnCheckIn.disabled = true;
                btnCheckIn.className = 'py-3 px-3 rounded-2xl bg-slate-200 text-slate-400 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                btnCheckInSub.textContent = 'Periksa lokasi dahulu';
            } else if (!isInsideRadius) {
                btnCheckIn.disabled = true;
                btnCheckIn.className = 'py-3 px-3 rounded-2xl bg-rose-100/70 text-rose-500 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                btnCheckInSub.textContent = 'Di luar area presensi';
            } else {
                btnCheckIn.disabled = false;
                btnCheckIn.className = 'py-3 px-3 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-md shadow-blue-500/25 transition cursor-pointer';
                btnCheckInSub.textContent = 'Kirim presensi masuk';
            }

            if (hasCheckedOut) {
                btnCheckOut.disabled = true;
                btnCheckOut.className = 'py-3 px-3 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                btnCheckOutSub.textContent = 'Sudah presensi pulang';
            } else if (!hasCheckedIn) {
                btnCheckOut.disabled = true;
                btnCheckOut.className = 'py-3 px-3 rounded-2xl bg-slate-200 text-slate-400 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                btnCheckOutSub.textContent = 'Belum absen masuk';
            } else if (!userCoordinates) {
                btnCheckOut.disabled = true;
                btnCheckOut.className = 'py-3 px-3 rounded-2xl bg-slate-200 text-slate-400 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                btnCheckOutSub.textContent = 'Periksa lokasi dahulu';
            } else if (!isInsideRadius) {
                btnCheckOut.disabled = true;
                btnCheckOut.className = 'py-3 px-3 rounded-2xl bg-rose-100/70 text-rose-500 font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-sm transition disabled:cursor-not-allowed';
                btnCheckOutSub.textContent = 'Di luar area presensi';
            } else {
                btnCheckOut.disabled = false;
                btnCheckOut.className = 'py-3 px-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white font-bold text-xs flex flex-col items-center justify-center gap-1 shadow-md shadow-indigo-500/25 transition cursor-pointer';
                btnCheckOutSub.textContent = 'Kirim presensi pulang';
            }
        }

        btnCheckLocation.addEventListener('click', function () {
            if (!navigator.geolocation) {
                showGpsError('Browser Tidak Mendukung', 'Browser Anda tidak mendukung Geolocation API.');
                return;
            }

            gpsStatusPill.textContent = 'Mengambil Lokasi...';
            gpsStatusPill.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200';
            btnCheckLocationText.textContent = 'Mendeteksi Satelit GPS...';
            btnGpsIcon.classList.add('animate-spin');
            gpsErrorBox.classList.add('hidden');

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    btnGpsIcon.classList.remove('animate-spin');
                    btnCheckLocationText.textContent = 'Perbarui Lokasi Saya';
                    processCoordinates(
                        position.coords.latitude,
                        position.coords.longitude,
                        position.coords.accuracy
                    );
                },
                function (error) {
                    btnGpsIcon.classList.remove('animate-spin');
                    btnCheckLocationText.textContent = 'Coba Periksa Ulang';
                    gpsStatusPill.textContent = 'Gagal Mengambil Lokasi';
                    gpsStatusPill.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200';

                    let title = 'Gagal Mengambil Lokasi';
                    let message = 'Terjadi kendala sensor GPS pada browser.';
                    if (error.code === error.PERMISSION_DENIED) {
                        title = 'Izin Lokasi Ditolak';
                        message = 'Harap aktifkan izin lokasi di pengaturan browser ponsel Anda.';
                    }
                    showGpsError(title, message);
                    userCoordinates = null;
                    areaStatusBadge.textContent = 'GPS Terkendala';
                    areaStatusBadge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200';
                    updateActionButtons();
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });

        function showGpsError(title, message) {
            document.getElementById('gpsErrorTitle').textContent = title;
            gpsErrorMessage.textContent = message;
            gpsErrorBox.classList.remove('hidden');
        }

        btnCheckIn.addEventListener('click', function () {
            if (!isInsideRadius || hasCheckedIn || !userCoordinates) return;

            btnCheckIn.disabled = true;
            btnCheckInSub.textContent = 'Menyimpan ke server...';

            fetch("{{ route('presensi.mobile.checkin') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    employee_id: employeeId,
                    latitude: userCoordinates.lat,
                    longitude: userCoordinates.lng,
                    notes: `Presensi masuk via Mobile GPS (${calculatedDistance}m)`
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    hasCheckedIn = true;
                    showToast('Presensi masuk berhasil dicatat!');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    showToast(data.message || 'Gagal presensi masuk.');
                    updateActionButtons();
                }
            })
            .catch(() => {
                showToast('Gagal menghubungi server.');
                updateActionButtons();
            });
        });

        btnCheckOut.addEventListener('click', function () {
            if (!isInsideRadius || !hasCheckedIn || hasCheckedOut || !userCoordinates) return;

            btnCheckOut.disabled = true;
            btnCheckOutSub.textContent = 'Menyimpan ke server...';

            fetch("{{ route('presensi.mobile.checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    employee_id: employeeId,
                    latitude: userCoordinates.lat,
                    longitude: userCoordinates.lng
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    hasCheckedOut = true;
                    showToast('Presensi pulang berhasil dicatat!');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    showToast(data.message || 'Gagal presensi pulang.');
                    updateActionButtons();
                }
            })
            .catch(() => {
                showToast('Gagal menghubungi server.');
                updateActionButtons();
            });
        });

        function showToast(msg) {
            toastMessage.textContent = msg;
            actionToast.classList.remove('hidden');
            setTimeout(() => actionToast.classList.add('hidden'), 4000);
        }

        updateActionButtons();
    });
</script>
@endpush
