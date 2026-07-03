<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OutputController;
use App\Http\Controllers\RealisasiController;
use App\Http\Controllers\CapaianController;
use App\Http\Controllers\KomponenController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RencanaKegiatanController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\AdminOnly;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(Authenticate::class)->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/ganti-tahun', [DashboardController::class, 'gantiTahun'])->name('ganti.tahun');

    // Output
    Route::get('/output', [OutputController::class, 'index'])->name('output.index');
    Route::post('/output', [OutputController::class, 'store'])->name('output.store');
    Route::put('/output/{output}', [OutputController::class, 'update'])->name('output.update');
    Route::delete('/output/{output}', [OutputController::class, 'destroy'])->name('output.destroy');
    Route::post('/output/import', [OutputController::class, 'import'])->name('output.import');
    Route::get('/output/template', [OutputController::class, 'templateImport'])->name('output.template');

    // Realisasi
    Route::get('/realisasi', [RealisasiController::class, 'index'])->name('realisasi.index');
    Route::get('/realisasi/{output}', [RealisasiController::class, 'detail'])->name('realisasi.detail');
    Route::put('/realisasi/{realisasi}/update', [RealisasiController::class, 'update'])->name('realisasi.update');

    // Capaian / Indikator Kinerja
    Route::get('/capaian', [CapaianController::class, 'index'])->name('capaian.index');
    Route::post('/capaian', [CapaianController::class, 'store'])->name('capaian.store');
    Route::put('/capaian/{capaian}', [CapaianController::class, 'update'])->name('capaian.update');
    Route::delete('/capaian/{capaian}', [CapaianController::class, 'destroy'])->name('capaian.destroy');
    Route::put('/capaian/bulanan/{realisasiBulanan}', [CapaianController::class, 'updateBulanan'])->name('capaian.bulanan.update');

    // Komponen Akuntabilitas (1=perencanaan, 2=pengukuran, 3=pelaporan, 4=evaluasi)
    Route::get('/komponen/{type}', [KomponenController::class, 'index'])->name('komponen.index')->where('type', 'perencanaan|pengukuran|pelaporan|evaluasi');
    Route::post('/komponen/{type}/store', [KomponenController::class, 'storeKomponen'])->name('komponen.store');
    Route::put('/komponen/item/{komponen}', [KomponenController::class, 'updateKomponen'])->name('komponen.update');
    Route::delete('/komponen/item/{komponen}', [KomponenController::class, 'destroyKomponen'])->name('komponen.destroy');
    Route::post('/subkomponen/{type}/store', [KomponenController::class, 'storeSubkomponen'])->name('subkomponen.store');
    Route::put('/subkomponen/{subkomponen}', [KomponenController::class, 'updateSubkomponen'])->name('subkomponen.update');
    Route::delete('/subkomponen/{subkomponen}', [KomponenController::class, 'destroySubkomponen'])->name('subkomponen.destroy');

    // Rencana Kegiatan
    Route::get('/rencana-kegiatan', [RencanaKegiatanController::class, 'index'])->name('rencana-kegiatan.index');
    Route::put('/rencana-kegiatan/{rencanaKegiatan}', [RencanaKegiatanController::class, 'update'])->name('rencana-kegiatan.update');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
    Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');

    // Users, Activity Log & Backup (admin only)
    Route::middleware(AdminOnly::class)->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
        Route::post('/backup', [BackupController::class, 'create'])->name('backup.create');
        Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
        Route::delete('/backup/{filename}', [BackupController::class, 'destroy'])->name('backup.destroy');
    });
});
