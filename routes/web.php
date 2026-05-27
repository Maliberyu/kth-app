<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KthController;
use App\Http\Controllers\PenyadapController;
use App\Http\Controllers\BlokController;
use App\Http\Controllers\ProduksiGetahController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\BpjsController;
 use App\Http\Controllers\PeriodeController;
 use App\Http\Controllers\BlokPetaController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KegiatanLuarController;
use App\Http\Controllers\KegiatanUsahaController;
use App\Http\Controllers\KegiatanPublicController;
use App\Http\Controllers\UsahaPublicController;
use App\Http\Controllers\KupsController;
use App\Http\Controllers\KomoditasController;

// =========================================================
// PUBLIC
// =========================================================
Route::get('/', fn() => redirect()->route('dashboard'));

// Registrasi kegiatan publik (scan QR — tanpa auth)
Route::get('/daftar/{token}',        [KegiatanPublicController::class, 'show'])->name('daftar.show');
Route::post('/daftar/{token}',       [KegiatanPublicController::class, 'store'])->name('daftar.store');
Route::get('/daftar/{token}/sukses', [KegiatanPublicController::class, 'sukses'])->name('daftar.sukses');

// Laporan transparansi kegiatan usaha (publik)
Route::get('/laporan-usaha/{token}', [UsahaPublicController::class, 'show'])->name('usaha.laporan');

// =========================================================
// AUTH (Breeze)
// =========================================================
require __DIR__.'/auth.php';

// =========================================================
// AUTHENTICATED
// =========================================================
Route::middleware(['auth'])->group(function () {

    // Dashboard (otomatis redirect sesuai role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =====================================================
    // SUPER ADMIN
    // =====================================================
    // 🔴 UBAH: Hapus 'role:super_admin', cek role di dalam KthController
    Route::middleware([/* 'role:super_admin' */])->prefix('super-admin')->name('super.')->group(function () {
        Route::resource('kth', KthController::class);
    });

    // =====================================================
    // ADMIN KTH
    // =====================================================
    // 🔴 UBAH: Hapus 'role:admin_kth', cek role di dalam masing-masing controller
    Route::middleware([/* 'role:admin_kth' */])->group(function () {
        // Surat Jalan
            Route::resource('surat-jalan', SuratJalanController::class);
            Route::patch('surat-jalan/{suratJalan}/kirim', [SuratJalanController::class, 'kirim'])->name('surat-jalan.kirim');
            Route::patch('surat-jalan/{suratJalan}/selesai', [SuratJalanController::class, 'selesai'])->name('surat-jalan.selesai');
            
            // 🔥 TAMBAH INI (DI DALAM GROUP YANG SAMA):
            Route::get('surat-jalan/{suratJalan}/cetak-pdf', [SuratJalanController::class, 'cetakPdf'])
              ->name('surat-jalan.cetak-pdf');
    
        // Penyadap
        Route::resource('penyadap', PenyadapController::class);

        // Blok
        Route::resource('blok', BlokController::class);

        // Produksi Getah
        Route::resource('produksi', ProduksiGetahController::class);
        Route::patch('produksi/{produksi}/validasi', [ProduksiGetahController::class, 'validasi'])
             ->name('produksi.validasi');

        // Surat Jalan
        Route::resource('surat-jalan', SuratJalanController::class);
        Route::patch('surat-jalan/{suratJalan}/kirim',  [SuratJalanController::class, 'kirim'])->name('surat-jalan.kirim');
        Route::patch('surat-jalan/{suratJalan}/selesai',[SuratJalanController::class, 'selesai'])->name('surat-jalan.selesai');

        // Penjualan
        Route::resource('penjualan', PenjualanController::class)->only(['index','create','store','show']);

        // Inventaris
        Route::get ('inventaris',                   [InventarisController::class, 'index'])->name('inventaris.index');
        Route::post('inventaris',                   [InventarisController::class, 'store'])->name('inventaris.store');
        Route::get ('inventaris/masuk',             [InventarisController::class, 'masukIndex'])->name('inventaris.masuk');
        Route::get ('inventaris/masuk/create',      [InventarisController::class, 'masukCreate'])->name('inventaris.masuk.create');
        Route::post('inventaris/masuk',             [InventarisController::class, 'masukStore'])->name('inventaris.masuk.store');
        Route::get ('inventaris/distribusi',        [InventarisController::class, 'distribusiIndex'])->name('inventaris.distribusi');
        Route::get ('inventaris/distribusi/create', [InventarisController::class, 'distribusiCreate'])->name('inventaris.distribusi.create');
        Route::post('inventaris/distribusi',        [InventarisController::class, 'distribusiStore'])->name('inventaris.distribusi.store');
        // blok
        Route::post('blok/{blok}/tugaskan', [BlokController::class, 'tugaskan'])->name('blok.tugaskan');
        Route::delete('blok/{blok}/hapus-tugas/{penyadap}', [BlokController::class, 'hapusTugas'])->name('blok.hapus-tugas');
        Route::patch('blok-peta/{blokPeta}/validasi', [BlokController::class, 'validasiPeta'])->name('blok.peta.validasi');
        //bpjs
        Route::post('penyadap/{penyadap}/bpjs', [BpjsController::class, 'store'])->name('penyadap.bpjs.store');
        Route::delete('bpjs/{bpjs}', [BpjsController::class, 'destroy'])->name('bpjs.destroy');

        // Kegiatan Dalam Ruangan
        Route::resource('kegiatan', KegiatanController::class);
        Route::get('kegiatan/{kegiatan}/export-excel',  [KegiatanController::class, 'exportExcel'])->name('kegiatan.export-excel');
        Route::get('kegiatan/{kegiatan}/export-pdf',    [KegiatanController::class, 'exportPdf'])->name('kegiatan.export-pdf');
        Route::post('kegiatan/{kegiatan}/upload-foto',  [KegiatanController::class, 'uploadFoto'])->name('kegiatan.upload-foto');
        Route::delete('kegiatan/{kegiatan}/hapus-foto', [KegiatanController::class, 'hapusFoto'])->name('kegiatan.hapus-foto');

        // Kegiatan Luar Ruangan
        Route::resource('kegiatan-luar', KegiatanLuarController::class);
        Route::post('kegiatan-luar/{kegiatanLuar}/foto',             [KegiatanLuarController::class, 'uploadFoto'])->name('kegiatan-luar.upload-foto');
        Route::delete('kegiatan-luar/{kegiatanLuar}/foto/{foto}',    [KegiatanLuarController::class, 'hapusFoto'])->name('kegiatan-luar.hapus-foto');
        Route::post('kegiatan-luar/{kegiatanLuar}/peserta',          [KegiatanLuarController::class, 'addPeserta'])->name('kegiatan-luar.add-peserta');
        Route::delete('kegiatan-luar/{kegiatanLuar}/peserta/{peserta}', [KegiatanLuarController::class, 'removePeserta'])->name('kegiatan-luar.remove-peserta');
        Route::get('kegiatan-luar/{kegiatanLuar}/export-excel',      [KegiatanLuarController::class, 'exportExcel'])->name('kegiatan-luar.export-excel');

        // Kegiatan Usaha
        Route::resource('kegiatan-usaha', KegiatanUsahaController::class);
        Route::post('kegiatan-usaha/{kegiatanUsaha}/modal',               [KegiatanUsahaController::class, 'addModal'])->name('kegiatan-usaha.add-modal');
        Route::delete('kegiatan-usaha/{kegiatanUsaha}/modal/{modal}',     [KegiatanUsahaController::class, 'removeModal'])->name('kegiatan-usaha.remove-modal');
        Route::post('kegiatan-usaha/{kegiatanUsaha}/transaksi',           [KegiatanUsahaController::class, 'addTransaksi'])->name('kegiatan-usaha.add-transaksi');
        Route::delete('kegiatan-usaha/{kegiatanUsaha}/transaksi/{transaksi}', [KegiatanUsahaController::class, 'removeTransaksi'])->name('kegiatan-usaha.remove-transaksi');
        Route::post('kegiatan-usaha/{kegiatanUsaha}/harian',              [KegiatanUsahaController::class, 'addHarian'])->name('kegiatan-usaha.add-harian');
        Route::delete('kegiatan-usaha/{kegiatanUsaha}/harian/{harian}',   [KegiatanUsahaController::class, 'removeHarian'])->name('kegiatan-usaha.remove-harian');
        Route::post('kegiatan-usaha/{kegiatanUsaha}/generate-qr',         [KegiatanUsahaController::class, 'generateQr'])->name('kegiatan-usaha.generate-qr');
        Route::patch('kegiatan-usaha/{kegiatanUsaha}/toggle-qr',          [KegiatanUsahaController::class, 'toggleQr'])->name('kegiatan-usaha.toggle-qr');
        // KUPS & Komoditas
        Route::resource('kups', KupsController::class);
        Route::get('kups/{kup}/print', [KupsController::class, 'printPdf'])->name('kups.print');
        Route::post('kups/{kup}/komoditas', [KomoditasController::class, 'store'])->name('kups.komoditas.store');
        Route::put('kups/{kup}/komoditas/{komoditas}', [KomoditasController::class, 'update'])->name('kups.komoditas.update');
        Route::delete('kups/{kup}/komoditas/{komoditas}', [KomoditasController::class, 'destroy'])->name('kups.komoditas.destroy');

        Route::get('kegiatan-usaha/{kegiatanUsaha}/print-harian',         [KegiatanUsahaController::class, 'printHarian'])->name('kegiatan-usaha.print-harian');
        Route::get('kegiatan-usaha/{kegiatanUsaha}/print-transaksi',      [KegiatanUsahaController::class, 'printTransaksi'])->name('kegiatan-usaha.print-transaksi');
    });

        // =====================================================
        // PENYADAP
        // =====================================================
        Route::middleware(['auth'])->prefix('saya')->name('saya.')->group(function () {

            // Mapping blok sendiri
            Route::get('blok', [BlokController::class, 'indexPenyadap'])->name('blok');
            Route::get('blok/{blok}', [BlokController::class, 'showPenyadap'])->name('blok.show');
            
            // ✅ SATU SAJA route untuk submit peta:
            Route::post('blok/{blok}/peta', [BlokController::class, 'simpanPeta'])
                ->name('blok.peta.store');  // → Full name: saya.blok.peta.store

            // Produksi sendiri
            Route::get('produksi', [ProduksiGetahController::class, 'indexPenyadap'])->name('produksi');
            Route::get('produksi/create', [ProduksiGetahController::class, 'createPenyadap'])->name('produksi.create');
            Route::post('produksi', [ProduksiGetahController::class, 'storePenyadap'])->name('produksi.store');
        });

    Route::get('/cek-session', function () {
        session(['test' => 'OK']);
        return session('test');
    });
   

    // Tambah di dalam group admin_kth
    Route::get('periode', [PeriodeController::class, 'index'])->name('periode.index');
    Route::post('periode', [PeriodeController::class, 'store'])->name('periode.store');
    Route::delete('periode/{periode}', [PeriodeController::class, 'destroy'])->name('periode.destroy');

    // Route::get('/cek-auth', function () {
    //     return auth()->check() ? 'LOGIN' : 'GUEST';
    // });
    //     Route::post('blok/{blok}/tugaskan', [BlokController::class, 'tugaskan'])->name('blok.tugaskan');
    //     Route::delete('blok/{blok}/hapus-tugas/{penyadap}', [BlokController::class, 'hapusTugas'])->name('blok.hapus-tugas');
    //     Route::patch('blok-peta/{blokPeta}/validasi', [BlokController::class, 'validasiPeta'])->name('blok.peta.validasi');
});