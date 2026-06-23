<?php

use App\Http\Controllers\DetailLayananTransaksiController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\Operator\OperatorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LayananCabangController;
use App\Http\Controllers\JenisLayananController;
use App\Http\Controllers\LayananTambahanController;
use App\Http\Controllers\JenisPakaianController;
use App\Http\Controllers\HargaJenisLayananController;
use App\Http\Controllers\LayananPrioritasController;
use App\Modules\Admin\Presentation\Web\Controllers\WebAdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [OperatorController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/riwayat-pesanan', [OperatorController::class, 'riwayatPesanan'])->name('admin.riwayat-pesanan');
        Route::get('/admin/riwayat-pesanan/{id}/proses', [OperatorController::class, 'prosesForm'])->name('admin.riwayat-pesanan.proses-form');
        Route::post('/admin/riwayat-pesanan/{id}/proses', [OperatorController::class, 'prosesTransaksi'])->name('admin.riwayat-pesanan.proses');
        Route::post('/admin/riwayat-pesanan/{id}/batal', [OperatorController::class, 'batalkanTransaksi'])->name('admin.riwayat-pesanan.batal');
        Route::get('/admin/gaji-karyawan', [OperatorController::class, 'gajiKaryawan'])->name('admin.gaji-karyawan');
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::get('/tambah', [UserController::class, 'create'])->name('user.create');
        Route::post('/simpan', [UserController::class, 'store'])->name('user.store');
        Route::get('/lihat/{user}', [UserController::class, 'view'])->name('user.view');
        Route::get('/ubah/{user}', [UserController::class, 'edit'])->name('user.edit');
        Route::post('/ubah/{user}', [UserController::class, 'update'])->name('user.update');
        Route::get('/ubah-password/{user}', [UserController::class, 'editPassword'])->name('user.edit.password');
        Route::post('/ubah-password/{slug}', [UserController::class, 'updatePassword'])->name('user.update.password');
        Route::post('/hapus', [UserController::class, 'delete'])->name('user.delete');
        Route::get('/trash/{user}', [UserController::class, 'trash'])->name('user.trash');
        Route::post('/pulih', [UserController::class, 'restore'])->name('user.restore');
        Route::post('/destroy', [UserController::class, 'destroy'])->name('user.destroy');
        Route::get('/cabang/{cabang}', [UserController::class, 'indexCabang'])->name('user.cabang');
        Route::get('/cabang/{cabang}/tambah', [UserController::class, 'createUserCabang'])->name('user.cabang.create');
        Route::post('/import', [UserController::class, 'import'])->name('user.import');
        Route::get('/export', [UserController::class, 'export'])->name('user.export');
    });

    Route::prefix('transaksi')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('transaksi');
        Route::get('/jadwal', [TransaksiController::class, 'indexJadwal'])->name('transaksi.jadwal');
        Route::get('/lihat/{transaksi}', [TransaksiController::class, 'viewDetailTransaksi'])->name('transaksi.view');
        Route::post('/lihat/{transaksi}/bukti-timbangan', [\App\Modules\Transaksi\Presentation\Web\Controllers\UploadBuktiTimbanganController::class, 'upload'])->name('transaksi.upload_bukti_timbangan');
        Route::get('/tambah', [TransaksiController::class, 'createTransaksiCabang'])->name('transaksi.create');
        Route::post('/simpan', [TransaksiController::class, 'storeTransaksiCabang'])->name('transaksi.store');
        Route::get('/ubah/{transaksi}', [TransaksiController::class, 'editTransaksiCabang'])->name('transaksi.edit');
        Route::post('/ubah/{transaksi}', [TransaksiController::class, 'updateTransaksiCabang'])->name('transaksi.update');
        Route::post('/hapus', [TransaksiController::class, 'deleteTransaksiCabang'])->name('transaksi.delete');
        Route::get('/status', [TransaksiController::class, 'editStatusTransaksiCabang'])->name('transaksi.edit.status');
        Route::post('/status', [TransaksiController::class, 'updateStatusTransaksiCabang'])->name('transaksi.update.status');
        Route::get('/jenis-pakaian', [TransaksiController::class, 'ubahJenisPakaian'])->name('transaksi.create.ubahJenisPakaian');
        Route::get('/jenis-layanan', [TransaksiController::class, 'ubahJenisLayanan'])->name('transaksi.create.ubahJenisLayanan');
        Route::get('/layanan-tambahan', [TransaksiController::class, 'ubahLayananTambahan'])->name('transaksi.create.ubahLayananTambahan');
        Route::get('/hitung-total-bayar', [TransaksiController::class, 'hitungTotalBayar'])->name('transaksi.create.hitungTotalBayar');
        Route::get('/struk/{transaksi}', [TransaksiController::class, 'cetakStrukTransaksi'])->name('transaksi.cetak-struk');
        Route::post('/konfirmasi-upah', [TransaksiController::class, 'konfirmasiUpah'])->name('transaksi.konfirmasiUpah');

        Route::get('/lurah', [TransaksiController::class, 'index'])->name('transaksi.lurah');
        Route::get('/lurah/{cabang}', [TransaksiController::class, 'indexCabang'])->name('transaksi.lurah.cabang');
        Route::get('/lurah/{cabang}/jadwal', [TransaksiController::class, 'indexCabangJadwal'])->name('transaksi.lurah.cabang.jadwal');
        Route::get('/lurah/{cabang}/lihat/{transaksi}', [TransaksiController::class, 'viewDetailTransaksi'])->name('transaksi.lurah.view');
        Route::get('/lurah/{cabang}/tambah', [TransaksiController::class, 'createTransaksiCabang'])->name('transaksi.lurah.cabang.create');
        Route::post('/lurah/{cabang}/simpan', [TransaksiController::class, 'storeTransaksiCabang'])->name('transaksi.lurah.cabang.store');
        Route::get('/lurah/{cabang}/ubah/{transaksi}', [TransaksiController::class, 'editTransaksiCabang'])->name('transaksi.lurah.cabang.edit');
        Route::post('/lurah/{cabang}/ubah/{transaksi}', [TransaksiController::class, 'updateTransaksiCabang'])->name('transaksi.lurah.cabang.update');
        Route::post('/lurah/{cabang}/hapus', [TransaksiController::class, 'deleteTransaksiCabang'])->name('transaksi.lurah.cabang.delete');
        Route::get('/lurah/{cabang}/status', [TransaksiController::class, 'editStatusTransaksiCabang'])->name('transaksi.lurah.cabang.edit.status');
        Route::post('/lurah/{cabang}/status', [TransaksiController::class, 'updateStatusTransaksiCabang'])->name('transaksi.lurah.cabang.update.status');
        Route::get('/lurah/{cabang}/jenis-pakaian', [TransaksiController::class, 'ubahJenisPakaian'])->name('transaksi.lurah.cabang.create.ubahJenisPakaian');
        Route::get('/lurah/{cabang}/jenis-layanan', [TransaksiController::class, 'ubahJenisLayanan'])->name('transaksi.lurah.cabang.create.ubahJenisLayanan');
        Route::get('/lurah/{cabang}/layanan-tambahan', [TransaksiController::class, 'ubahLayananTambahan'])->name('transaksi.lurah.cabang.create.ubahLayananTambahan');
        Route::get('/lurah/{cabang}/hitung-total-bayar', [TransaksiController::class, 'hitungTotalBayar'])->name('transaksi.lurah.cabang.create.hitungTotalBayar');
    });

    Route::get('/transaksi/{transaksi}/layanan/{detailTransaksi}', [DetailLayananTransaksiController::class, 'viewDetailLayanan'])
        ->name('transaksi.view.layanan');

    Route::get('/transaksi/lurah/{cabang}/lihat/{transaksi}/layanan/{detailTransaksi}', [DetailLayananTransaksiController::class, 'viewDetailLayanan'])
        ->name('transaksi.lurah.view.layanan');

    Route::prefix('laporan')->group(function () {
        Route::get('/pendapatan-laundry', [\App\Http\Controllers\LaporanController::class, 'laporanPendapatanLaundry'])->name('laporan.pendapatan.laundry');
        Route::post('/pendapatan-laundry/pdf', [\App\Http\Controllers\LaporanController::class, 'pdfLaporanPendapatanLaundry'])->name('laporan.pendapatan.laundry.pdf');
        Route::get('/pelanggan', [\App\Http\Controllers\LaporanController::class, 'laporanPelanggan'])->name('laporan.pelanggan');
        Route::post('/pelanggan/pdf', [\App\Http\Controllers\LaporanController::class, 'pdfLaporanPelanggan'])->name('laporan.pelanggan.pdf');
    });

    // Layanan Cabang
    Route::group([
        'prefix' => 'layanan-cabang',
        'middleware' => ['role:lurah|pic'],
    ], function() {
        Route::get('/', [LayananCabangController::class, 'index'])->name('layanan-cabang');
        Route::get('/{cabang}', [LayananCabangController::class, 'indexCabang'])->name('layanan-cabang.cabang');
        Route::get('/{cabang}/trash', [LayananCabangController::class, 'indexCabangTrash'])->name('layanan-cabang.cabang.trash');
    });

    // Jenis Layanan
    Route::group([
        'prefix' => 'jenis-layanan',
        'middleware' => ['role:lurah|manajer_laundry|pic'],
    ], function() {
        Route::get('/', [JenisLayananController::class, 'index'])->name('jenis-layanan');
        Route::post('/tambah', [JenisLayananController::class, 'store'])->name('jenis-layanan.store');
        Route::get('/lihat', [JenisLayananController::class, 'show'])->name('jenis-layanan.show');
        Route::get('/ubah', [JenisLayananController::class, 'edit'])->name('jenis-layanan.edit');
        Route::post('/ubah', [JenisLayananController::class, 'update'])->name('jenis-layanan.update');
        Route::post('/hapus', [JenisLayananController::class, 'delete'])->name('jenis-layanan.delete');
        Route::get('/trash', [JenisLayananController::class, 'trash'])->name('jenis-layanan.trash');
        Route::post('/pulihkan', [JenisLayananController::class, 'restore'])->name('jenis-layanan.restore');
        Route::post('/hapus-permanen', [JenisLayananController::class, 'destroy'])->name('jenis-layanan.destroy');
        Route::post('/impor', [JenisLayananController::class, 'import'])->name('jenis-layanan.import');
        Route::get('/ekspor', [JenisLayananController::class, 'export'])->name('jenis-layanan.export');
    });

    // Layanan Tambahan
    Route::group([
        'prefix' => 'layanan-tambahan',
        'middleware' => ['role:lurah|manajer_laundry|pic'],
    ], function() {
        Route::get('/', [LayananTambahanController::class, 'index'])->name('layanan-tambahan');
        Route::post('/tambah', [LayananTambahanController::class, 'store'])->name('layanan-tambahan.store');
        Route::get('/lihat', [LayananTambahanController::class, 'show'])->name('layanan-tambahan.show');
        Route::get('/ubah', [LayananTambahanController::class, 'edit'])->name('layanan-tambahan.edit');
        Route::post('/ubah', [LayananTambahanController::class, 'update'])->name('layanan-tambahan.update');
        Route::post('/hapus', [LayananTambahanController::class, 'delete'])->name('layanan-tambahan.delete');
        Route::get('/trash', [LayananTambahanController::class, 'trash'])->name('layanan-tambahan.trash');
        Route::post('/pulihkan', [LayananTambahanController::class, 'restore'])->name('layanan-tambahan.restore');
        Route::post('/hapus-permanen', [LayananTambahanController::class, 'destroy'])->name('layanan-tambahan.destroy');
        Route::post('/impor', [LayananTambahanController::class, 'import'])->name('layanan-tambahan.import');
        Route::get('/ekspor', [LayananTambahanController::class, 'export'])->name('layanan-tambahan.export');
    });

    // Jenis Pakaian
    Route::group([
        'prefix' => 'jenis-pakaian',
        'middleware' => ['role:lurah|manajer_laundry|pic'],
    ], function() {
        Route::get('/', [JenisPakaianController::class, 'index'])->name('jenis-pakaian');
        Route::post('/tambah', [JenisPakaianController::class, 'store'])->name('jenis-pakaian.store');
        Route::get('/lihat', [JenisPakaianController::class, 'show'])->name('jenis-pakaian.show');
        Route::get('/ubah', [JenisPakaianController::class, 'edit'])->name('jenis-pakaian.edit');
        Route::post('/ubah', [JenisPakaianController::class, 'update'])->name('jenis-pakaian.update');
        Route::post('/hapus', [JenisPakaianController::class, 'delete'])->name('jenis-pakaian.delete');
        Route::get('/trash', [JenisPakaianController::class, 'trash'])->name('jenis-pakaian.trash');
        Route::post('/pulihkan', [JenisPakaianController::class, 'restore'])->name('jenis-pakaian.restore');
        Route::post('/hapus-permanen', [JenisPakaianController::class, 'destroy'])->name('jenis-pakaian.destroy');
        Route::post('/impor', [JenisPakaianController::class, 'import'])->name('jenis-pakaian.import');
        Route::get('/ekspor', [JenisPakaianController::class, 'export'])->name('jenis-pakaian.export');
    });

    // Harga Jenis Layanan
    Route::group([
        'prefix' => 'harga-jenis-layanan',
        'middleware' => ['role:lurah|manajer_laundry|pic'],
    ], function() {
        Route::get('/', [HargaJenisLayananController::class, 'index'])->name('harga-jenis-layanan');
        Route::post('/tambah', [HargaJenisLayananController::class, 'store'])->name('harga-jenis-layanan.store');
        Route::get('/lihat', [HargaJenisLayananController::class, 'show'])->name('harga-jenis-layanan.show');
        Route::get('/ubah', [HargaJenisLayananController::class, 'edit'])->name('harga-jenis-layanan.edit');
        Route::post('/ubah', [HargaJenisLayananController::class, 'update'])->name('harga-jenis-layanan.update');
        Route::post('/hapus', [HargaJenisLayananController::class, 'delete'])->name('harga-jenis-layanan.delete');
        Route::get('/trash', [HargaJenisLayananController::class, 'trash'])->name('harga-jenis-layanan.trash');
        Route::post('/pulihkan', [HargaJenisLayananController::class, 'restore'])->name('harga-jenis-layanan.restore');
        Route::post('/hapus-permanen', [HargaJenisLayananController::class, 'destroy'])->name('harga-jenis-layanan.destroy');
        Route::post('/impor', [HargaJenisLayananController::class, 'import'])->name('harga-jenis-layanan.import');
        Route::get('/ekspor', [HargaJenisLayananController::class, 'export'])->name('harga-jenis-layanan.export');
    });

    // Layanan Prioritas
    Route::group([
        'prefix' => 'layanan-prioritas',
        'middleware' => ['role:lurah|manajer_laundry|pic'],
    ], function() {
        Route::get('/', [LayananPrioritasController::class, 'index'])->name('layanan-prioritas');
        Route::post('/tambah', [LayananPrioritasController::class, 'store'])->name('layanan-prioritas.store');
        Route::get('/lihat', [LayananPrioritasController::class, 'show'])->name('layanan-prioritas.show');
        Route::get('/ubah', [LayananPrioritasController::class, 'edit'])->name('layanan-prioritas.edit');
        Route::post('/ubah', [LayananPrioritasController::class, 'update'])->name('layanan-prioritas.update');
        Route::post('/hapus', [LayananPrioritasController::class, 'delete'])->name('layanan-prioritas.delete');
        Route::get('/trash', [LayananPrioritasController::class, 'trash'])->name('layanan-prioritas.trash');
        Route::post('/pulihkan', [LayananPrioritasController::class, 'restore'])->name('layanan-prioritas.restore');
        Route::post('/hapus-permanen', [LayananPrioritasController::class, 'destroy'])->name('layanan-prioritas.destroy');
        Route::post('/impor', [LayananPrioritasController::class, 'import'])->name('layanan-prioritas.import');
        Route::get('/ekspor', [LayananPrioritasController::class, 'export'])->name('layanan-prioritas.export');
    });

});

