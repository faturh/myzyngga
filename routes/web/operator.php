<?php

use App\Http\Controllers\DetailLayananTransaksiController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\Operator\OperatorController;
use App\Modules\Admin\Presentation\Web\Controllers\WebAdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [OperatorController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/riwayat-pesanan', [OperatorController::class, 'riwayatPesanan'])->name('admin.riwayat-pesanan');
        Route::get('/admin/riwayat-pesanan/{id}/proses', [OperatorController::class, 'prosesForm'])->name('admin.riwayat-pesanan.proses-form');
        Route::post('/admin/riwayat-pesanan/{id}/proses', [OperatorController::class, 'prosesTransaksi'])->name('admin.riwayat-pesanan.proses');
        Route::post('/admin/riwayat-pesanan/{id}/batal', [OperatorController::class, 'batalkanTransaksi'])->name('admin.riwayat-pesanan.batal');
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

});

