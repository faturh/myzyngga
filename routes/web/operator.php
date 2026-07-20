<?php

use App\Http\Controllers\Operator\OperatorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [OperatorController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/riwayat-pesanan', [OperatorController::class, 'riwayatPesanan'])->name('admin.riwayat-pesanan');
        Route::get('/admin/riwayat-pesanan/tambah', [OperatorController::class, 'tambahPesananForm'])->name('admin.riwayat-pesanan.tambah-form');
        Route::post('/admin/riwayat-pesanan/tambah', [OperatorController::class, 'storePesananForm'])->name('admin.riwayat-pesanan.store');
        Route::get('/admin/riwayat-pesanan/{id}/proses', [OperatorController::class, 'prosesForm'])->name('admin.riwayat-pesanan.proses-form');
        Route::post('/admin/riwayat-pesanan/{id}/proses', [OperatorController::class, 'prosesTransaksi'])->name('admin.riwayat-pesanan.proses');
        Route::post('/admin/riwayat-pesanan/{id}/batal', [OperatorController::class, 'batalkanTransaksi'])->name('admin.riwayat-pesanan.batal');
        Route::get('/admin/riwayat-pesanan/{id}/kerjakan', [OperatorController::class, 'kerjakanForm'])->name('admin.riwayat-pesanan.kerjakan-form');
        Route::post('/admin/riwayat-pesanan/{id}/kerjakan', [OperatorController::class, 'kerjakanTransaksi'])->name('admin.riwayat-pesanan.kerjakan');
        Route::post('/admin/riwayat-pesanan/{id}/konfirmasi-upgrade', [OperatorController::class, 'konfirmasiUpgrade'])->name('admin.riwayat-pesanan.konfirmasi-upgrade');
        Route::post('/admin/riwayat-pesanan/{id}/inisiasi-upgrade', [OperatorController::class, 'inisiasiUpgrade'])->name('admin.riwayat-pesanan.inisiasi-upgrade');
        Route::post('/admin/riwayat-pesanan/{id}/selesaikan', [OperatorController::class, 'selesaikanPengerjaan'])->name('admin.riwayat-pesanan.selesaikan');
        Route::post('/admin/riwayat-pesanan/{id}/selesaikan-antar', [OperatorController::class, 'selesaikanAntar'])->name('admin.riwayat-pesanan.selesaikan-antar');
        Route::post('/admin/riwayat-pesanan/{id}/konfirmasi-jemput', [OperatorController::class, 'konfirmasiJemput'])->name('admin.riwayat-pesanan.konfirmasi-jemput');
        Route::post('/admin/riwayat-pesanan/{id}/konfirmasi-bayar', [OperatorController::class, 'konfirmasiBayar'])->name('admin.riwayat-pesanan.konfirmasi-bayar');
        Route::post('/admin/riwayat-pesanan/kendala/{id}/selesai', [OperatorController::class, 'selesaikanComplaint'])->name('admin.riwayat-pesanan.selesaikan-kendala');
        Route::get('/admin/riwayat-pesanan/counts', [OperatorController::class, 'getRealtimeCounts'])->name('admin.riwayat-pesanan.counts');
        Route::post('/admin/riwayat-pesanan/{transaksi}/bukti-timbangan', [\App\Modules\Transaksi\Presentation\Web\Controllers\UploadBuktiTimbanganController::class, 'upload'])->name('admin.riwayat-pesanan.bukti-timbangan');
        Route::get('/admin/gaji-karyawan', [OperatorController::class, 'gajiKaryawan'])->name('admin.gaji-karyawan');
        Route::get('/admin/gaji-karyawan/download', [OperatorController::class, 'downloadGajiKaryawan'])->name('admin.gaji-karyawan.download');
        Route::post('/admin/gaji-karyawan/bayar', [OperatorController::class, 'bayarGaji'])->name('admin.gaji-karyawan.bayar');
        Route::post('/admin/gaji-karyawan/update-tarif', [OperatorController::class, 'updateTarifGaji'])->name('admin.gaji-karyawan.update-tarif');
        
        Route::get('/admin/keuangan', [\App\Modules\Transaksi\Presentation\Web\Controllers\KeuanganController::class, 'index'])->name('admin.keuangan');
        Route::post('/admin/keuangan', [\App\Modules\Transaksi\Presentation\Web\Controllers\KeuanganController::class, 'store'])->name('admin.keuangan.store');
        Route::delete('/admin/keuangan/{id}', [\App\Modules\Transaksi\Presentation\Web\Controllers\KeuanganController::class, 'destroy'])->name('admin.keuangan.destroy');
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

    Route::prefix('laporan')->group(function () {
        Route::get('/pendapatan-laundry', [LaporanController::class, 'laporanPendapatanLaundry'])->name('laporan.pendapatan.laundry');
        Route::post('/pendapatan-laundry/pdf', [LaporanController::class, 'pdfLaporanPendapatanLaundry'])->name('laporan.pendapatan.laundry.pdf');
        Route::get('/pelanggan', [LaporanController::class, 'laporanPelanggan'])->name('laporan.pelanggan');
        Route::get('/pelanggan-list', [LaporanController::class, 'laporanPelanggan'])->name('pelanggan');
        Route::post('/pelanggan/pdf', [LaporanController::class, 'pdfLaporanPelanggan'])->name('laporan.pelanggan.pdf');
    });
});
