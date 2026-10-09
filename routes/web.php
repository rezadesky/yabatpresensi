<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\WorkScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', function () {
    return redirect()->route('login');
});
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ========================================================
// RUTE TERPROTEKSI (WAJIB LOGIN UNTUK MENGAKSES)
// ========================================================
Route::middleware('auth')->group(function () {
    // Portal Mobile Pegawai YABAT (4 Tabs)
    Route::prefix('mobile')->name('mobile.')->group(function () {
        Route::get('/beranda', [AttendanceController::class, 'mobileBeranda'])->name('beranda');
        Route::get('/presensi', [AttendanceController::class, 'mobile'])->name('presensi');
        Route::get('/riwayat', [AttendanceController::class, 'mobileRiwayat'])->name('riwayat');
        Route::get('/profil', [AttendanceController::class, 'mobileProfil'])->name('profil');
    });

    // Alias & API Endpoint Presensi GPS
    Route::get('/presensi-mobile', [AttendanceController::class, 'mobile'])->name('presensi.mobile');
    Route::post('/presensi-mobile/checkin', [AttendanceController::class, 'checkIn'])->name('presensi.mobile.checkin');
    Route::post('/presensi-mobile/checkout', [AttendanceController::class, 'checkOut'])->name('presensi.mobile.checkout');

    // Admin Panel Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // 1. Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/locations', [DashboardController::class, 'updateLocations'])->name('dashboard.locations');

    // 2. Data Pegawai CRUD
    Route::get('/pegawai', [EmployeeController::class, 'index'])->name('pegawai');
    Route::post('/pegawai', [EmployeeController::class, 'store'])->name('pegawai.store');
    Route::put('/pegawai/{id}', [EmployeeController::class, 'update'])->name('pegawai.update');
    Route::delete('/pegawai/{id}', [EmployeeController::class, 'destroy'])->name('pegawai.destroy');

    // 3. Institusi Unit (Read-only daftar unit tetap yayasan)
    Route::get('/institusi', [InstitutionController::class, 'index'])->name('institusi');

    // 4. Jadwal Kerja CRUD
    Route::get('/jadwal', [WorkScheduleController::class, 'index'])->name('jadwal');
    Route::post('/jadwal', [WorkScheduleController::class, 'store'])->name('jadwal.store');
    Route::put('/jadwal/{id}', [WorkScheduleController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [WorkScheduleController::class, 'destroy'])->name('jadwal.destroy');

    // 5. Presensi Monitoring & API
    Route::get('/presensi', [AttendanceController::class, 'monitor'])->name('presensi');
    Route::post('/presensi/checkin', [AttendanceController::class, 'checkIn'])->name('presensi.checkin');
    Route::post('/presensi/checkout', [AttendanceController::class, 'checkOut'])->name('presensi.checkout');

    // 6. Riwayat Presensi
    Route::get('/riwayat', [AttendanceController::class, 'history'])->name('riwayat');

    // 7. Laporan Rekapitulasi (Bulanan, Tahunan, Kustom & Ekspor Excel)
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan');
    Route::get('/laporan/export-excel', [ReportController::class, 'exportExcel'])->name('laporan.export');

        // 8. Pengaturan Sistem
        Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan');
        Route::post('/pengaturan', [SettingController::class, 'update'])->name('pengaturan.update');
    });
});

