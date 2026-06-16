<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    /**
     * Display the operator admin dashboard with dynamic metrics.
     */
    public function dashboard()
    {
        $perluDiprosesCount = Operator::getPerluDiprosesCount();
        $menungguPembayaranCount = Operator::getMenungguPembayaranCount();
        $perluDikerjakanCount = Operator::getPerluDikerjakanCount();
        $pesananSelesaiCount = Operator::getPesananSelesaiCount();

        return view('operator.admin.dashboard', compact(
            'perluDiprosesCount',
            'menungguPembayaranCount',
            'perluDikerjakanCount',
            'pesananSelesaiCount'
        ));
    }

    /**
     * Display the detailed order history (Riwayat Pesanan) page.
     */
    public function riwayatPesanan(Request $request)
    {
        $tab = $request->query('tab', 'perlu-diproses');
        $search = $request->query('search');

        // Dynamic badges count
        $perluDiprosesCount = Operator::getPerluDiprosesCount();
        $menungguPembayaranCount = Operator::getMenungguPembayaranCount();
        $perluDikerjakanCount = Operator::getPerluDikerjakanCount();
        $pesananSelesaiCount = Operator::getPesananSelesaiCount();

        // Query setup
        $query = Transaksi::query()
            ->with(['pelanggan.user', 'pegawai', 'cabang']);

        // Search filter (Nomor Pesanan / Nota or Pelanggan Name)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nota', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($pq) use ($search) {
                      $pq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Tab filter
        switch ($tab) {
            case 'perlu-diproses':
                $query->whereIn('status', ['Baru', 'created']);
                break;
            case 'menunggu-pembayaran':
                $query->where('status', 'Proses')
                      ->where('payment_status', 'pending');
                break;
            case 'perlu-dikerjakan':
                $query->where('status', 'Proses')
                      ->where('payment_status', 'paid');
                break;
            case 'selesai':
                $query->where('status', 'Selesai');
                break;
            case 'kendala':
            case 'dibatalkan':
                // Force empty result as per instruction: "sisanya kendala dan sedang di batalkan itu kosongin aja"
                $query->whereRaw('1 = 0');
                break;
            case 'semua':
            default:
                // Return all
                break;
        }

        $transaksi = $query->latest('waktu')->paginate(10)->withQueryString();

        return view('operator.admin.riwayat-pesanan', compact(
            'transaksi',
            'tab',
            'search',
            'perluDiprosesCount',
            'menungguPembayaranCount',
            'perluDikerjakanCount',
            'pesananSelesaiCount'
        ));
    }

    /**
     * Process order (update status to 'Proses').
     */
    public function prosesTransaksi(string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->status = 'Proses';
        $transaksi->save();

        return redirect()->back()->with('success', 'Pesanan #' . $transaksi->nota . ' berhasil diproses.');
    }

    /**
     * Cancel order (update status to 'Batal').
     */
    public function batalkanTransaksi(string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->status = 'Batal';
        $transaksi->save();

        return redirect()->back()->with('success', 'Pesanan #' . $transaksi->nota . ' berhasil dibatalkan.');
    }
}
