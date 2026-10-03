<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\StokBahanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ResepMenuController;

Route::get('/export-pdf/laba-rugi', [PdfController::class, 'labaRugi'])->name('export.laba-rugi');
Route::get('/export-pdf/laporan-keuangan', [PdfController::class, 'laporanKeuangan'])->name('export.laporan-keuangan');
Route::get('/export-pdf/neraca',           [PdfController::class, 'neraca'])->name('export.neraca');

Route::get('/export-pdf', [PdfController::class, 'exportAll'])->name('export.pdf');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transaksi terpisah
    Route::get('/pemasukan',        [TransaksiController::class, 'pemasukan'])->name('pemasukan.index');
    Route::get('/pengeluaran',      [TransaksiController::class, 'pengeluaran'])->name('pengeluaran.index');
    Route::post('/transaksi',       [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::delete('/transaksi/{transaksi}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');

    Route::resource('stok-bahan', StokBahanController::class);
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // === POS (Kasir) ===
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/struk/{id}', [PosController::class, 'struk'])->name('pos.struk');
    Route::get('/pos/riwayat', [PosController::class, 'riwayat'])->name('pos.riwayat');

    // === Resep Menu (atur bahan per menu untuk sinkronisasi stok) ===
    Route::get('/resep', [ResepMenuController::class, 'index'])->name('resep.index');
    Route::get('/resep/{menu}/edit', [ResepMenuController::class, 'edit'])->name('resep.edit');
    Route::put('/resep/{menu}', [ResepMenuController::class, 'update'])->name('resep.update');
    Route::post('/resep/bahan-baru', [ResepMenuController::class, 'storeBahanBaru'])->name('resep.bahan-baru'); // <-- TAMBAHKAN INI
});

require __DIR__.'/auth.php';