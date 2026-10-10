<div align="center">
  <img src="public/logo.png" alt="Logo YABAT PRESENSI" width="120" style="margin-bottom: 12px;"/>
  <h1>YABAT PRESENSI</h1>
  <p><strong>Sistem Presensi Pegawai Berbasis Geofencing GPS Real-Time & Panel Manajemen Terpadu</strong></p>
  <p><em>Yayasan Anak Bangsa Aceh Tenggara (YABAT) &bull; STKIP Usman Safri Kutacane</em></p>

  <p>
    <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
    <img src="https://img.shields.io/badge/React_Native-Expo_SDK_57-000020?style=for-the-badge&logo=expo&logoColor=white" alt="Expo SDK 57">
    <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
    <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="TailwindCSS">
    <img src="https://img.shields.io/badge/Sanctum-Token_Auth-blue?style=for-the-badge" alt="Sanctum">
  </p>
</div>

---

## 📖 Tentang Proyek

**YABAT PRESENSI** adalah ekosistem aplikasi presensi terintegrasi yang dirancang khusus untuk memenuhi kebutuhan absensi tenaga pendidik dan staf di lingkungan Yayasan Anak Bangsa Aceh Tenggara. 

Sistem ini menerapkan model arsitektur terpisah (*Dual-Channel Role Architecture*):
1. **Portal Web Browser**: Dikhususkan eksklusif untuk **Administrator Yayasan** guna mengelola master data pegawai, memantau kehadiran harian secara *live*, mengatur titik koordinat GPS & radius geofence unit institusi, serta mencetak rekap laporan absensi.
2. **Aplikasi Mobile (Android Standalone APK)**: Dikhususkan untuk **Pegawai** melakukan presensi masuk dan pulang menggunakan sensor GPS *native high-accuracy* dengan proteksi anti-kecurangan (*Anti-Fake GPS / Mock Location Detection*).

---

## 🌟 Fitur Utama

### 📱 1. Mobile App Pegawai (Pure React Native Expo)
- **100% Native GPS Geolocation**: Menggunakan modul `expo-location` dengan akurasi meter tinggi dan popup paksa pengaktifan GPS sistem (*System Location Dialog*).
- **Dual Geofence Haversine**: Penghitungan jarak langsung di HP dan divalidasi ulang di backend untuk memastikan pegawai berada di dalam radius resmi unit kerja.
- **Anti-Fraud & Anti-Fake GPS**: Deteksi otomatis aplikasi lokasi tiruan (`isMocked` check) di perangkat Android.
- **4 Tab Navigasi Terpadu**:
  - **Beranda**: Jam operasional digital *Live WIB*, ringkasan status kehadiran harian, dan informasi jadwal kerja resmi.
  - **Presensi**: Titik sensor GPS real-time, status radius geofencing, tombol presensi masuk/pulang instan, dan panduan jarak.
  - **Riwayat**: Filter rekapitulasi kehadiran (*Bulan Ini*, *Minggu Ini*, *Semua*), kartu metrik statistik (*Hadir, Telat, Izin, Alpa*), dan log harian.
  - **Profil**: Detail identitas pegawai, NIP/NIDN, unit institusi naungan, kontak, dan opsi keamanan logout.
- **Real-Time Auto Refresh (`useFocusEffect`)**: Status presensi langsung ter-update otomatis saat berpindah tab tanpa perlu reload manual.
- **Dynamic Safe Area Insets**: Bebas bug poni notch, punch-hole camera, dan terhindar dari tabrakan System Navigation Bar Android.
- **Light Theme Splash**: Tampilan awal bersih dan profesional dengan logo resmi beresolusi tinggi.

### 💻 2. Portal Manajemen Web Administrator
- **Dashboard Real-Time**: Rekap persentase kehadiran hari ini, statistik keterlambatan, dan grafik tren mingguan.
- **Pengaturan Koordinat Titik Kampus Terpadu**: Kemudahan mengubah titik Latitude, Longitude, dan Radius Toleransi (Meter) langsung dari dashboard admin untuk seluruh unit sekolah/kampus.
- **Manajemen Data Pegawai**: CRUD data pegawai terhubung dengan akun login NIP/Email dan status keaktifan.
- **Manajemen Jadwal Kerja**: Konfigurasi jam masuk, jam pulang, toleransi keterlambatan menit, dan hari kerja.
- **Laporan & Ekspor Excel**: Rekapitulasi absensi bulanan dan rentang tanggal dengan ekspor spreadsheet resmi.
- **Restriksi Akses Browser**: Perlindungan otomatis yang menolak akses browser bagi pegawai dan mengarahkannya untuk menggunakan aplikasi APK resmi.

---

## 🏗️ Struktur Direktori Proyek

```text
yabatpresensi/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── MobileApiController.php    # RESTful API Sanctum khusus Mobile App
│   │   │   ├── AuthController.php             # Login Admin & proteksi browser
│   │   │   ├── DashboardController.php        # Panel Dashboard & update lokasi GPS
│   │   │   ├── AttendanceController.php       # Monitoring & rekap presensi admin
│   │   │   └── EmployeeController.php         # Manajemen master data pegawai
│   │   └── Middleware/
│   │       └── EnsureUserIsAdmin.php          # Middleware proteksi role admin
│   └── Models/
│       ├── Attendance.php                     # Model log kehadiran (lat, lng, in, out)
│       ├── Employee.php                       # Model data pegawai
│       ├── Institution.php                    # Model institusi & titik koordinat GPS
│       └── WorkSchedule.php                   # Model jam kerja & toleransi
├── resources/
│   └── views/                                 # Antarmuka web Blade & TailwindCSS
│       ├── admin/                             # Halaman Dashboard, Pegawai, Laporan
│       ├── welcome.blade.php                  # Halaman Login Portal Admin
│       └── mobile_notice.blade.php            # Halaman info penonaktifan browser HP
├── routes/
│   ├── api.php                                # Endpoint API Mobile (/api/mobile/*)
│   └── web.php                                # Rute web browser portal admin
│
└── yabat-apk/                                 # Proyek Mobile React Native (Expo)
    ├── assets/                                # Aset logo transparan, icon & splash
    ├── src/
    │   ├── api/
    │   │   └── client.js                      # Axios instance + auto Bearer Token
    │   ├── constants/
    │   │   └── theme.js                       # Palette warna resmi YABAT
    │   ├── context/
    │   │   └── AuthContext.js                 # State session login & data pegawai
    │   ├── utils/
    │   │   └── helpers.js                     # Rumus Haversine, date ID & time WIB
    │   ├── components/
    │   │   └── MobileHeader.js                # Header pegawai dark theme + ambient glow
    │   ├── navigation/
    │   │   ├── AppNavigator.js                # Auth routing flow
    │   │   └── MainTabNavigator.js            # 4 Bottom Tab Bar dinamis
    │   └── screens/
    │       ├── LoginScreen.js                 # Login pegawai (Email/NIP)
    │       ├── BerandaScreen.js               # Beranda jam digital & jadwal
    │       ├── PresensiScreen.js              # Presensi GPS & geofence validasi
    │       ├── RiwayatScreen.js               # Riwayat presensi & statistik
    │       └── ProfilScreen.js                # Profil lengkap & preferensi
    ├── app.json                               # Konfigurasi package & permission Android
    ├── eas.json                               # Konfigurasi Cloud Build APK standalone
    └── App.js                                 # Root component + SafeAreaProvider
```

---

## 🚀 Panduan Instalasi & Menjalankan

### Persyaratan Sistem
- PHP >= 8.1 dengan ekstensi PDO, OpenSSL, Mbstring
- Composer
- Node.js >= 18.x & NPM
- MySQL Database

---

### A. Menjalankan Backend Laravel

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/rezadesky/yabatpresensi.git
   cd yabatpresensi
   ```

2. **Install Dependensi PHP**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Sesuaikan konfigurasi database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) pada file `.env`.*

4. **Migrasi Database & Seeder**:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```

---

### B. Menjalankan Aplikasi Mobile (React Native Expo)

1. **Masuk ke Direktori Mobile**:
   ```bash
   cd yabat-apk
   ```

2. **Install Dependensi Node.js**:
   ```bash
   npm install
   ```

3. **Jalankan Metro Dev Server**:
   ```bash
   npx expo start -c
   ```

4. **Buka di HP Android**:
   - Buka aplikasi **Expo Go** di HP Android.
   - Scan QR code yang tampil di terminal atau masukkan URL Metro (misal: `exp://192.168.x.x:8081`).

---

### C. Build File APK Standalone (Siap Pasang di HP)

Aplikasi telah dikonfigurasi dengan Expo Application Services (EAS):

```bash
cd yabat-apk
npx eas build --platform android --profile preview
```
Setelah proses cloud build selesai, Anda akan mendapatkan tautan unduhan langsung file `.apk`.

---

## 🔒 Alur Keamanan & Anti-Fraud

```
[Pegawai Klik Presensi]
         │
         ├──> 1. Deteksi Mock Location di HP (isMocked === true?)
         │        ├── Ya  --> Tampilkan Peringatan & Kunci Tombol
         │        └── Tidak
         │
         ├──> 2. Pengecekan GPS Service (GPS Aktif?)
         │        ├── Tidak --> Munculkan Dialog Sistem Android untuk Menyalakan GPS
         │        └── Ya
         │
         ├──> 3. Hitung Jarak Haversine di Perangkat (Jarak <= Radius?)
         │        ├── Tidak --> Tombol Tetap Non-Aktif
         │        └── Ya    --> Kirim Koordinat ke API (/api/mobile/checkin)
         │
         └──> 4. Validasi Ulang di Server Laravel (Backend)
                  ├── Verifikasi is_mocked (False)
                  ├── Hitung ulang Haversine dengan koordinat resmi database
                  ├── Cek toleransi jadwal kerja (Set status Hadir / Terlambat)
                  └── Simpan Log Presensi ke Database MySQL
```

---

## 👥 Hak Cipta & Pengembang

Dikembangkan untuk **Yayasan Anak Bangsa Aceh Tenggara (YABAT)** & **STKIP Usman Safri Kutacane**.  
© 2026 Seluruh Hak Cipta Dilindungi.
