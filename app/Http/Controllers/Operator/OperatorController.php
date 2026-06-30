<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\Transaksi;
use Illuminate\Http\Request;

use App\Modules\Transaksi\Application\Services\TimbanganService;

class OperatorController extends Controller
{
    public function __construct(
        private readonly TimbanganService $prosesService
    ) {}

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
     * Display the employee salary sorting and management page.
     */
    public function gajiKaryawan()
    {
        $karyawan = \App\Models\User::role(['manajer_laundry', 'pegawai_laundry', 'gamis', 'pic', 'lurah'])
            ->with('roles')
            ->get();

        return view('operator.admin.gaji-karyawan', compact('karyawan'));
    }

    /**
     * Display the detailed order history (Riwayat Pesanan) page.
     */
    public function riwayatPesanan(Request $request)
    {
        $tab = $request->query('tab', 'perlu-diproses');
        $search = $request->query('search');
        $sort = $request->query('sort', 'deadline');

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

        // Apply sorting
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        if ($sort === 'deadline') {
            if ($driver === 'pgsql') {
                $query->join('layanan_prioritas as lp', 'lp.id', '=', 'transaksi.layanan_prioritas_id')
                      ->select('transaksi.*')
                      ->orderByRaw("
                          transaksi.waktu + (
                              CASE 
                                  WHEN LOWER(lp.nama) = 'kilat' THEN INTERVAL '5 hours'
                                  WHEN LOWER(lp.nama) = 'express' THEN INTERVAL '10 hours'
                                  WHEN LOWER(lp.nama) = 'quick' THEN INTERVAL '20 hours'
                                  ELSE INTERVAL '30 hours'
                              END
                          ) ASC
                      ");
            } else {
                $query->join('layanan_prioritas as lp', 'lp.id', '=', 'transaksi.layanan_prioritas_id')
                      ->select('transaksi.*')
                      ->orderByRaw("
                          datetime(transaksi.waktu, 
                              CASE 
                                  WHEN LOWER(lp.nama) = 'kilat' THEN '+5 hours'
                                  WHEN LOWER(lp.nama) = 'express' THEN '+10 hours'
                                  WHEN LOWER(lp.nama) = 'quick' THEN '+20 hours'
                                  ELSE '+30 hours'
                              END
                          ) ASC
                      ");
            }
        } elseif ($sort === 'terbaru') {
            $query->orderBy('waktu', 'desc');
        } elseif ($sort === 'terlama') {
            $query->orderBy('waktu', 'asc');
        } elseif ($sort === 'prioritas_desc') {
            $query->join('layanan_prioritas as lp', 'lp.id', '=', 'transaksi.layanan_prioritas_id')
                  ->select('transaksi.*')
                  ->orderBy('lp.prioritas', 'desc');
        } else {
            $query->orderBy('waktu', 'desc');
        }

        $transaksi = $query->paginate(10)->withQueryString();

        return view('operator.admin.riwayat-pesanan', compact(
            'transaksi',
            'tab',
            'search',
            'sort',
            'perluDiprosesCount',
            'menungguPembayaranCount',
            'perluDikerjakanCount',
            'pesananSelesaiCount'
        ));
    }

    /**
     * Show the order processing form.
     */
    public function prosesForm(string $id)
    {
        try {
            $transaksi = $this->prosesService->getProsesFormData($id);
            
            // Fetch available laundry items (JenisPakaian)
            $itemsAvailable = \App\Models\JenisPakaian::get();

            return view('operator.admin.proses-transaksi', compact('transaksi', 'itemsAvailable'));
        } catch (\Exception $e) {
            return redirect()->route('admin.riwayat-pesanan')->with('error', $e->getMessage());
        }
    }

    /**
     * Process order (update status to 'Proses' and store items/weights).
     */
    public function prosesTransaksi(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'actual_weight' => 'required|numeric|min:0.01',
                'minimum_weight' => 'required|numeric|min:0',
                'price_per_kg' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.nama_item' => 'required|string|max:255',
                'items.*.qty' => 'required|integer|min:1',
            ], [
                'actual_weight.required' => 'Berat timbangan wajib diisi.',
                'actual_weight.min' => 'Berat timbangan harus lebih besar dari 0 kg.',
                'items.required' => 'List item laundry minimal harus berisi satu item.',
                'items.min' => 'List item laundry minimal harus berisi satu item.',
                'items.*.nama_item.required' => 'Nama item laundry wajib diisi.',
                'items.*.qty.min' => 'Jumlah/Qty item minimal adalah 1.',
            ]);

            $this->prosesService->prosesTransaksi($id, $validated);

            $transaksi = Transaksi::findOrFail($id);
            return redirect()->route('admin.riwayat-pesanan')->with('success', 'Pesanan #' . $transaksi->nota . ' berhasil diproses.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
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
