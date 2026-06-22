<?php

namespace App\Modules\Order\Presentation\Web\Controllers;

use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use Illuminate\Http\Request;

class PublicStrukController
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {
    }

    public function __invoke(Request $request, string $idOrNota)
    {
        // Coba cari berdasarkan ID dulu jika formatnya UUID, jika gagal atau bukan UUID coba berdasarkan nota
        $transaksi = null;
        if (\Illuminate\Support\Str::isUuid($idOrNota)) {
            $transaksi = $this->orders->findById($idOrNota);
        }
        
        if (!$transaksi) {
            $transaksi = $this->orders->findByNotaPelanggan($idOrNota);
        }

        abort_unless($transaksi, 404, 'Transaksi tidak ditemukan.');

        // Load semua relasi yang diperlukan oleh struk.index view
        $transaksi->load([
            'pegawai',
            'pelanggan',
            'layananPrioritas',
            'cabang',
            'detailTransaksi.detailLayananTransaksi.hargaJenisLayanan.jenisLayanan',
            'detailTransaksi.detailLayananTransaksi.hargaJenisLayanan.jenisPakaian',
            'layananTambahanTransaksi.layananTambahan'
        ]);

        $cabang = $transaksi->cabang;
        $detailTransaksi = $transaksi->detailTransaksi;
        $layananTambahanTransaksi = $transaksi->layananTambahanTransaksi;

        $title = 'Cetak Struk';

        return view('operator.dashboard.transaksi.struk.index', compact(
            'title',
            'transaksi',
            'detailTransaksi',
            'cabang',
            'layananTambahanTransaksi'
        ));
    }
}
