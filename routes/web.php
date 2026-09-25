<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\RekomendasiKesehatanController;
use App\Http\Controllers\UjiAkurasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// --- Auth ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// --- Modul inti (semua butuh login) ---
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan.index');
    Route::get('/pemasukan/baru', [PemasukanController::class, 'create'])->name('pemasukan.create');
    Route::post('/pemasukan', [PemasukanController::class, 'store'])->name('pemasukan.store');
    Route::delete('/pemasukan/{pemasukan}', [PemasukanController::class, 'destroy'])->name('pemasukan.destroy');

    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
    Route::get('/pengeluaran/baru', [PengeluaranController::class, 'create'])->name('pengeluaran.create');
    Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
    Route::delete('/pengeluaran/{pengeluaran}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');

    // Fitur inti skripsi: rekomendasi kesehatan keuangan berbasis Generative AI
    Route::get('/rekomendasi', [RekomendasiKesehatanController::class, 'index'])->name('rekomendasi.index');
    Route::post('/rekomendasi', [RekomendasiKesehatanController::class, 'store'])->name('rekomendasi.store');
    Route::get('/rekomendasi/{rekomendasi}', [RekomendasiKesehatanController::class, 'show'])->name('rekomendasi.show');

    // Instrumen uji akurasi (BAB III Testing): AI vs penilaian manual
    Route::get('/uji-akurasi', [UjiAkurasiController::class, 'index'])->name('uji-akurasi.index');
    Route::post('/uji-akurasi/{rekomendasi}', [UjiAkurasiController::class, 'store'])->name('uji-akurasi.store');
    Route::delete('/uji-akurasi/penilaian/{penilaian}', [UjiAkurasiController::class, 'destroy'])->name('uji-akurasi.destroy');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');
});
