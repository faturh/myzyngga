<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\Transaksi;
use Illuminate\Http\Request;

use App\Modules\Transaksi\Application\Services\TimbanganService;
use App\Modules\Transaksi\Application\Services\KeuanganService;

class OperatorController extends Controller
{
    public function __construct(
        private readonly TimbanganService $prosesService,
        private readonly KeuanganService $keuanganService
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

        $user = auth()->user();
        $cabangId = $user->hasRole('manajer_laundry') ? $user->cabang_id : null;

        $saldoToko = $this->keuanganService->getStoreBalance($cabangId);

        return view('operator.admin.dashboard', compact(
            'perluDiprosesCount',
            'menungguPembayaranCount',
            'perluDikerjakanCount',
            'pesananSelesaiCount',
            'saldoToko'
        ));
    }

    /**
     * Display the employee salary sorting and management page.
     */
    public function gajiKaryawan(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $karyawanList = \App\Models\User::role('pegawai_laundry')->get();

        $karyawanData = $karyawanList->map(function($emp) use ($startDate, $endDate) {
            $completedTransactions = \App\Models\Transaksi::query()
                ->where('pegawai_id', $emp->id)
                ->whereIn('status', ['Pesanan Selesai', 'Selesai'])
                ->whereBetween('waktu', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->with('timbangan')
                ->get();

            $totalKg = $completedTransactions->sum(function($t) {
                return $t->timbangan?->actual_weight ?? 0;
            });

            $tarifGaji = (int) ($emp->gaji ?? 0);
            $totalGaji = $totalKg * $tarifGaji;

            return [
                'id' => $emp->id,
                'name' => $emp->name ?? $emp->username,
                'role' => 'Pegawai Laundry',
                'gaji_per_kg' => $tarifGaji,
                'total_kg' => $totalKg,
                'total_gaji' => $totalGaji,
                'initial' => strtoupper(substr($emp->name ?? $emp->username, 0, 2)),
            ];
        });

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $karyawanData,
                'status' => 200
            ], 200);
        }

        return view('operator.admin.gaji-karyawan', [
            'karyawan' => $karyawanData,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Download the employee salary recap spreadsheet.
     */
    public function downloadGajiKaryawan(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $karyawanList = \App\Models\User::role('pegawai_laundry')->get();

        $karyawanData = $karyawanList->map(function($emp) use ($startDate, $endDate) {
            $completedTransactions = \App\Models\Transaksi::query()
                ->where('pegawai_id', $emp->id)
                ->whereIn('status', ['Pesanan Selesai', 'Selesai'])
                ->whereBetween('waktu', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->with('timbangan')
                ->get();

            $totalKg = $completedTransactions->sum(function($t) {
                return $t->timbangan?->actual_weight ?? 0;
            });

            $tarifGaji = (int) ($emp->gaji ?? 0);
            $totalGaji = $totalKg * $tarifGaji;

            return [
                'id' => $emp->id,
                'name' => $emp->name ?? $emp->username,
                'role' => 'Pegawai Laundry',
                'gaji_per_kg' => $tarifGaji,
                'total_kg' => $totalKg,
                'total_gaji' => $totalGaji,
            ];
        })->toArray();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\GajiKaryawanExport($karyawanData),
            'Rekap_Gaji_Karyawan_' . $startDate . '_to_' . $endDate . '.xlsx'
        );
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
        $prosesPengerjaanCount = Operator::getProsesPengerjaanCount();
        $pesananSelesaiCount = Operator::getPesananSelesaiCount();
        $kendalaPesananCount = Operator::getKendalaPesananCount();
        $sedangDibatalkanCount = Operator::getSedangDibatalkanCount();
        $sedangDijemputCount = Operator::getSedangDijemputCount();

        // Query setup
        $query = Transaksi::query()
            ->with(['pelanggan.user', 'pegawai', 'cabang', 'layananPrioritas']);

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
                $query->whereHas('listPengerjaan', fn($q) => $q->where('list_status_pengerjaan_id', 1));
                break;
            case 'menunggu-pembayaran':
                $query->whereIn('status', ['Menunggu Pembayaran', 'Pesanan Selesai', 'Selesai'])
                      ->where('payment_status', '!=', 'paid');
                break;
            case 'perlu-dikerjakan':
                $query->whereHas('listPengerjaan', fn($q) => $q->where('list_status_pengerjaan_id', 3));
                break;
            case 'proses-pengerjaan':
                $query->whereHas('listPengerjaan', fn($q) => $q->where('list_status_pengerjaan_id', 4));
                break;
            case 'selesai':
                $query->whereIn('status', ['Menunggu Pembayaran', 'Pesanan Selesai', 'Selesai'])
                      ->where('payment_status', 'paid');
                break;
            case 'kendala':
                $query->whereHas('listPengerjaan', fn($q) => $q->where('list_status_pengerjaan_id', 6));
                break;
            case 'dibatalkan':
                $query->whereHas('listPengerjaan', fn($q) => $q->where('list_status_pengerjaan_id', 7));
                break;
            case 'sedang-dijemput':
                $query->whereHas('listPengerjaan', fn($q) => $q->where('list_status_pengerjaan_id', 8));
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

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $transaksi,
                'status' => 200
            ], 200);
        }

        return view('operator.admin.riwayat-pesanan', compact(
            'transaksi',
            'tab',
            'search',
            'sort',
            'perluDiprosesCount',
            'menungguPembayaranCount',
            'perluDikerjakanCount',
            'prosesPengerjaanCount',
            'pesananSelesaiCount',
            'kendalaPesananCount',
            'sedangDibatalkanCount',
            'sedangDijemputCount'
        ));
    }

    /**
     * Show the order processing form.
     */
    public function prosesForm(string $id)
    {
        try {
            $transaksi = Transaksi::with([
                'pelanggan.user',
                'layananPrioritas',
                'timbangan',
                'detailTransaksi',
                'upgradeLayanans.layananAsal',
                'upgradeLayanans.layananTujuan'
            ])->findOrFail($id);
            
            // Decode pending upgrade
            $meta = json_decode($transaksi->payment_metadata, true) ?? [];
            $pendingUpgrade = $meta['pending_upgrade'] ?? null;
            if ($pendingUpgrade) {
                $targetService = \App\Models\LayananPrioritas::find($pendingUpgrade['new_service_id']);
                $pendingUpgrade['target_service_name'] = $targetService ? $targetService->nama : '-';
            }

            $upgradeHistory = $transaksi->upgradeLayanans;

            $currentPriority = $transaksi->layananPrioritas;
            $availableUpgrades = collect();
            if ($currentPriority) {
                $availableUpgrades = \App\Models\LayananPrioritas::where('cabang_id', $currentPriority->cabang_id)
                    ->where('prioritas', '>', $currentPriority->prioritas)
                    ->get();
            }

            // Fetch available Satuan items
            $satuanItemsAvailable = \App\Models\KategoriPakaianSatuan::get();

            return view('operator.admin.proses-transaksi', compact(
                'transaksi',
                'satuanItemsAvailable',
                'pendingUpgrade',
                'upgradeHistory',
                'availableUpgrades'
            ));
        } catch (\Exception $e) {
            return redirect()->route('admin.riwayat-pesanan')->with('error', $e->getMessage());
        }
    }

    /**
     * Process order (update status to 'Proses' and store items/weights).
     */
    public function prosesTransaksi(Request $request, string $id)
    {
        if ($request->has('berat') && !$request->has('actual_weight')) {
            $request->merge(['actual_weight' => $request->input('berat')]);
        }

        try {
            $validated = $request->validate([
                'tipe_layanan' => 'nullable|string',
                'actual_weight' => 'nullable|numeric|min:0',
                'minimum_weight' => 'nullable|numeric|min:0',
                'price_per_kg' => 'nullable|numeric|min:0',
                'satuan_items' => 'nullable|array',
                'satuan_items.*.kategori_pakaian_satuan_id' => 'required_with:satuan_items|exists:kategori_pakaian_satuan,id',
                'satuan_items.*.jumlah' => 'required_with:satuan_items|integer|min:1',
            ], [
                'satuan_items.*.kategori_pakaian_satuan_id.required_with' => 'Kategori satuan wajib dipilih.',
                'satuan_items.*.jumlah.min' => 'Jumlah satuan minimal adalah 1.',
            ]);

            // Ensure either actual_weight is filled or satuan_items are added
            $hasWeight = $request->filled('actual_weight') && (double) $request->input('actual_weight') > 0;
            $hasSatuan = false;
            if ($request->filled('satuan_items') && is_array($request->input('satuan_items'))) {
                foreach ($request->input('satuan_items') as $item) {
                    if (!empty($item['kategori_pakaian_satuan_id'])) {
                        $hasSatuan = true;
                        break;
                    }
                }
            }

            if (!$hasWeight && !$hasSatuan) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Silakan isi berat timbangan kiloan ATAU masukkan minimal satu item satuan tambahan.',
                        'status' => 400
                    ], 400);
                }
                return redirect()->back()->withInput()->with('error', 'Silakan isi berat timbangan kiloan ATAU masukkan minimal satu item satuan tambahan.');
            }

            $this->prosesService->prosesTransaksi($id, $validated);

            $transaksi = Transaksi::findOrFail($id);
            $transaksi->load(['layananPrioritas', 'timbangan.items.jenisPakaian', 'pegawai']);
            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $transaksi,
                    'message' => 'Pesanan #' . $transaksi->nota . ' berhasil diproses.',
                    'status' => 200
                ], 200);
            }
            return redirect()->route('admin.riwayat-pesanan')->with('success', 'Pesanan #' . $transaksi->nota . ' berhasil diproses.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Gagal memproses pesanan: ' . $e->getMessage(),
                    'status' => 400
                ], 400);
            }
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the order start work form.
     */
    public function kerjakanForm(string $id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);
            
            // Retrieve employees of this branch
            $pegawaiList = \App\Models\User::where('cabang_id', $transaksi->cabang_id)
                ->whereNotIn('role', ['customer', 'admin'])
                ->get();
                
            $itemsAvailable = \App\Models\JenisPakaian::get();

            return view('operator.admin.proses-pekerjaan', compact('transaksi', 'pegawaiList', 'itemsAvailable'));
        } catch (\Exception $e) {
            return redirect()->route('admin.riwayat-pesanan')->with('error', $e->getMessage());
        }
    }

    /**
     * Start working on transaction (update status to 'Proses Pengerjaan').
     */
    public function kerjakanTransaksi(Request $request, string $id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);
            $layananNama = strtolower($transaksi->layananPrioritas->nama ?? 'reguler');

            $rules = [
                'pegawai_id' => 'required|exists:users,id',
            ];

            if ($layananNama !== 'satuan') {
                $rules['items'] = 'required|array|min:1';
                $rules['items.*.nama_item'] = 'required|string|max:255';
                $rules['items.*.qty'] = 'required|integer|min:1';
            } else {
                $rules['items'] = 'nullable|array';
                $rules['items.*.nama_item'] = 'nullable|string|max:255';
                $rules['items.*.qty'] = 'nullable|integer|min:1';
            }

            $validated = $request->validate($rules, [
                'pegawai_id.required' => 'Karyawan penanggung jawab wajib dipilih.',
                'items.required' => 'List rincian pakaian laundry kiloan wajib diisi.',
                'items.min' => 'List rincian pakaian laundry kiloan minimal berisi satu item.',
                'items.*.nama_item.required' => 'Nama item laundry wajib diisi.',
                'items.*.qty.min' => 'Jumlah/Qty item minimal adalah 1.',
            ]);

            $transaksi->pegawai_id = (string) $validated['pegawai_id'];

            // Save/Update clothes list in list_pakaian_timbangan
            $timbangan = $transaksi->timbangan;
            if (!$timbangan) {
                $timbangan = \App\Models\Timbangan::create([
                    'transaksi_id' => $transaksi->id,
                    'nota' => $transaksi->nota,
                    'actual_weight' => 0,
                    'minimum_weight' => 0,
                    'price_per_kg' => 0,
                    'charged_weight' => 0,
                    'total_price' => $transaksi->total_biaya_layanan,
                ]);
            }

            $timbangan->items()->delete();
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    if (!empty($item['nama_item'])) {
                        $jenisPakaian = \App\Models\JenisPakaian::firstOrCreate([
                            'nama' => trim($item['nama_item'])
                        ]);

                        $timbangan->items()->create([
                            'jenis_pakaian_id' => $jenisPakaian->id,
                            'qty' => $item['qty'],
                        ]);
                    }
                }
            }

            $transaksi->status = 'proses pengerjaan';
            $transaksi->save();
            $transaksi->load(['layananPrioritas', 'timbangan.items.jenisPakaian', 'pegawai']);

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $transaksi,
                    'message' => 'Pesanan #' . $transaksi->nota . ' mulai dikerjakan oleh ' . $transaksi->pegawai->name . '.',
                    'status' => 200
                ], 200);
            }
            return redirect()->route('admin.riwayat-pesanan')->with('success', 'Pesanan #' . $transaksi->nota . ' mulai dikerjakan oleh ' . $transaksi->pegawai->name . '.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Gagal mulai mengerjakan pesanan: ' . $e->getMessage(),
                    'status' => 400
                ], 400);
            }
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

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $transaksi,
                'message' => 'Pesanan #' . $transaksi->nota . ' berhasil dibatalkan.',
                'status' => 200
            ], 200);
        }
        return redirect()->back()->with('success', 'Pesanan #' . $transaksi->nota . ' berhasil dibatalkan.');
    }

    /**
     * Complete transaction pengerjaan (update status to 'selesai').
     */
    public function selesaikanPengerjaan(string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->status = 'selesai';
        $transaksi->save();

        $message = 'Pengerjaan pesanan #' . $transaksi->nota . ' telah selesai.';
        if ($transaksi->list_status_pengerjaan_id == 2) {
            $message .= ' Menunggu pelunasan pembayaran dari pelanggan.';
        } else {
            $message .= ' Pembayaran sudah lunas, status menjadi Selesai.';
        }

        if (request()->expectsJson()) {
            return response()->json([
                'data' => $transaksi,
                'message' => $message,
                'status' => 200
            ], 200);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Show the manual order creation form.
     */
    public function tambahPesananForm()
    {
        // 1. Fetch available LayananPrioritas
        $prioritasList = \App\Models\LayananPrioritas::orderBy('id')->get();

        // 2. Fetch list of registered customers
        $pelangganList = \App\Models\Pelanggan::orderBy('nama')->get();

        // 3. Fetch list of branch employees (excluding customer role)
        $pegawaiList = \App\Models\User::whereNotIn('role', ['customer'])->orderBy('name')->get();

        return view('operator.admin.tambah-pesanan', compact('prioritasList', 'pelangganList', 'pegawaiList'));
    }

    /**
     * Store the manual order.
     */
    public function storePesananForm(Request $request)
    {
         $validated = $request->validate([
             'pelanggan_option' => 'required|string|in:existing,new',
             'pelanggan_id' => 'required_if:pelanggan_option,existing|nullable|exists:pelanggan,id',
             'customer_name' => 'required_if:pelanggan_option,new|nullable|string|max:255',
             'customer_phone' => 'required_if:pelanggan_option,new|nullable|string|max:20',
             'customer_address' => 'nullable|string|max:500',
             'layanan_prioritas_id' => 'required|exists:layanan_prioritas,id',
             'parfum' => 'nullable|string|max:255',
             'catatan' => 'nullable|string',
             'jenis_pembayaran' => 'required|string|in:cash,qris,transfer',
             'payment_status' => 'required|string|in:pending,paid',
             'pegawai_id' => 'required|exists:users,id',
         ], [
             'pelanggan_id.required_if' => 'Pelanggan lama wajib dipilih.',
             'customer_name.required_if' => 'Nama pelanggan baru wajib diisi.',
             'customer_phone.required_if' => 'Telepon pelanggan baru wajib diisi.',
             'layanan_prioritas_id.required' => 'Jenis layanan prioritas wajib dipilih.',
             'jenis_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
             'payment_status.required' => 'Status pembayaran wajib dipilih.',
             'pegawai_id.required' => 'Karyawan penanggung jawab wajib dipilih.',
         ]);
 
         try {
             // 1. Resolve Pelanggan
             $pelanggan = null;
             if ($validated['pelanggan_option'] === 'existing') {
                 $pelanggan = \App\Models\Pelanggan::findOrFail($validated['pelanggan_id']);
             } else {
                // Create new user profile for customer
                $uniqueStr = time() . '_' . rand(100, 999);
                $newUser = \App\Models\User::create([
                    'username' => 'pelanggan_' . $uniqueStr,
                    'email' => 'pelanggan_' . $uniqueStr . '@zyngga.com',
                    'name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'],
                    'slug' => 'pelanggan-' . $uniqueStr,
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'customer',
                ]);
                $newUser->assignRole('customer');

                $pelanggan = \App\Models\Pelanggan::create([
                    'user_id' => $newUser->id,
                    'nama' => $validated['customer_name'],
                    'jenis_kelamin' => 'L',
                    'telepon' => $validated['customer_phone'],
                    'alamat' => $request->customer_address ?? 'Outlet / Walk-in',
                ]);
            }

            // 2. Resolve pricing
            $layanan = \App\Models\LayananPrioritas::findOrFail($validated['layanan_prioritas_id']);
            $defaultCost = strtolower($layanan->nama) === 'satuan' ? 10000 : 4850;

            // 3. Create Transaksi
            $suffix = strtoupper(substr(str_replace('-', '', (string) \Illuminate\Support\Str::uuid()), 0, 8));
            $nota = 'PLG-' . $suffix;

            $transaksi = \App\Models\Transaksi::create([
                'nota' => $nota,
                'waktu' => now(),
                'pickup_address' => $request->customer_address ?? 'Outlet / Walk-in',
                'pickup_detail_address' => null,
                'pickup_date' => now()->toDateString(),
                'pickup_time' => now()->format('H:i:s'),
                'parfum' => $validated['parfum'] ?? 'Standard',
                'catatan' => $validated['catatan'],
                'total_biaya_layanan' => $defaultCost,
                'total_biaya_prioritas' => 0,
                'total_biaya_layanan_tambahan' => 0,
                'total_bayar_akhir' => $defaultCost,
                'jenis_pembayaran' => $validated['jenis_pembayaran'],
                'payment_status' => $validated['payment_status'],
                'paid_at' => $validated['payment_status'] === 'paid' ? now() : null,
                'bayar' => $validated['payment_status'] === 'paid' ? $defaultCost : 0,
                'kembalian' => 0,
                'status' => 'Perlu Diproses',
                'layanan_prioritas_id' => $layanan->id,
                'pelanggan_id' => $pelanggan->id,
                'pegawai_id' => $validated['pegawai_id'],
                'cabang_id' => auth()->user()->cabang_id ?? \App\Models\Cabang::value('id') ?? 1,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $transaksi,
                    'message' => 'Pesanan Manual #' . $transaksi->nota . ' berhasil dibuat.',
                    'status' => 200
                ], 200);
            }

            return redirect()->route('admin.riwayat-pesanan')->with('success', 'Pesanan Manual #' . $transaksi->nota . ' berhasil dibuat.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Gagal membuat pesanan: ' . $e->getMessage(),
                    'status' => 400
                ], 400);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi upgrade layanan yang tertunda (pending) dengan pembayaran Tunai.
     */
    public function konfirmasiUpgrade(Request $request, string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $meta = json_decode($transaksi->payment_metadata, true) ?? [];

        if (!isset($meta['pending_upgrade'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tidak ada permintaan upgrade tertunda untuk pesanan ini.',
                    'status' => 400
                ], 400);
            }
            return redirect()->back()->with('error', 'Tidak ada permintaan upgrade tertunda untuk pesanan ini.');
        }

        $pending = $meta['pending_upgrade'];
        $newServiceId = (int) $pending['new_service_id'];
        $priceDiff = (double) $pending['price_diff'];

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($transaksi, $meta, $newServiceId, $priceDiff) {
                // 1. Cancel Midtrans transaction if snap token was generated
                if (isset($meta['midtrans_order_id'])) {
                    try {
                        \Midtrans\Config::$serverKey = config('midtrans.server_key');
                        \Midtrans\Config::$isProduction = config('midtrans.is_production');
                        \Midtrans\Transaction::cancel($meta['midtrans_order_id']);
                    } catch (\Exception $e) {
                        // Ignore cancel error
                    }
                }

                // 2. Create upgrade record with Cash payment method
                \App\Models\UpgradeLayanan::create([
                    'transaksi_id' => $transaksi->id,
                    'layanan_asal_id' => $transaksi->layanan_prioritas_id,
                    'layanan_tujuan_id' => $newServiceId,
                    'biaya_upgrade' => $priceDiff,
                    'metode_bayar' => 'Tunai',
                ]);

                // 3. Update transaction price and service
                $transaksi->layanan_prioritas_id = $newServiceId;
                $transaksi->total_biaya_prioritas = (double) $transaksi->total_biaya_prioritas + $priceDiff;
                $transaksi->total_bayar_akhir = (double) $transaksi->total_bayar_akhir + $priceDiff;
                $transaksi->bayar = (double) $transaksi->bayar + $priceDiff; // Tambah cash yang dibayarkan

                // 4. Clean up pending upgrade from metadata
                unset($meta['pending_upgrade']);
                if (isset($meta['midtrans_order_id'])) {
                    unset($meta['midtrans_order_id']);
                }
                $transaksi->payment_metadata = empty($meta) ? null : json_encode($meta);

                // 5. Update payment status if fully paid
                if ($transaksi->bayar >= $transaksi->total_bayar_akhir) {
                    $transaksi->payment_status = 'paid';
                    if (!$transaksi->paid_at) {
                        $transaksi->paid_at = now();
                    }
                }

                $transaksi->save();

                // 6. Record list history pengerjaan
                $history = new \App\Models\ListHistoryPengerjaan();
                $history->transaksi_id = $transaksi->id;
                $history->status_sebelumnya = $transaksi->listPengerjaan?->list_status_pengerjaan_id;
                $history->status_sesudahnya = $transaksi->listPengerjaan?->list_status_pengerjaan_id;
                $history->operator_id = auth()->id();
                $history->keterangan = "Upgrade tunai dikonfirmasi operator: Layanan diubah ke ID {$newServiceId}. Selisih Rp " . number_format($priceDiff, 0, ',', '.') . " diterima tunai.";
                $history->save();
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $transaksi,
                    'message' => 'Upgrade layanan berhasil dikonfirmasi dan pembayaran tunai dicatat.',
                    'status' => 200
                ], 200);
            }
            return redirect()->back()->with('success', 'Upgrade layanan berhasil dikonfirmasi dan pembayaran tunai dicatat.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Gagal konfirmasi upgrade: ' . $e->getMessage(),
                    'status' => 400
                ], 400);
            }
            return redirect()->back()->with('error', 'Gagal konfirmasi upgrade: ' . $e->getMessage());
        }
    }

    /**
     * Operator melakukan inisiasi upgrade layanan secara langsung dengan pembayaran tunai di tempat.
     */
    public function inisiasiUpgrade(Request $request, string $id)
    {
        $request->validate([
            'new_service_id' => 'required|exists:layanan_prioritas,id',
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $newService = \App\Models\LayananPrioritas::findOrFail($request->new_service_id);

        if ($newService->prioritas <= ($transaksi->layananPrioritas->prioritas ?? 0)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Layanan tujuan harus memiliki prioritas lebih tinggi.',
                    'status' => 400
                ], 400);
            }
            return redirect()->back()->with('error', 'Layanan tujuan harus memiliki prioritas lebih tinggi.');
        }

        // Calculate price diff using weight
        $weight = 1.0;
        if ($transaksi->timbangan) {
            $weight = max(3.0, (double) $transaksi->timbangan->actual_weight);
        } else {
            // fallback: check if clothes/items are registered
            $weight = $transaksi->detailTransaksi->sum('total_pakaian') ?: 1.0;
        }

        $priceDiff = max(0, ((double) $newService->harga - (double) ($transaksi->layananPrioritas->harga ?? 0)) * $weight);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($transaksi, $newService, $priceDiff) {
                // 1. Create upgrade record
                \App\Models\UpgradeLayanan::create([
                    'transaksi_id' => $transaksi->id,
                    'layanan_asal_id' => $transaksi->layanan_prioritas_id,
                    'layanan_tujuan_id' => $newService->id,
                    'biaya_upgrade' => $priceDiff,
                    'metode_bayar' => 'Tunai',
                ]);

                // 2. Update transaction
                $transaksi->layanan_prioritas_id = $newService->id;
                $transaksi->total_biaya_prioritas = (double) $transaksi->total_biaya_prioritas + $priceDiff;
                $transaksi->total_bayar_akhir = (double) $transaksi->total_bayar_akhir + $priceDiff;
                $transaksi->bayar = (double) $transaksi->bayar + $priceDiff; // Tambah cash yang dibayarkan

                // 3. Update payment status if fully paid
                if ($transaksi->bayar >= $transaksi->total_bayar_akhir) {
                    $transaksi->payment_status = 'paid';
                    if (!$transaksi->paid_at) {
                        $transaksi->paid_at = now();
                    }
                }

                $transaksi->save();

                // 4. Record history
                $history = new \App\Models\ListHistoryPengerjaan();
                $history->transaksi_id = $transaksi->id;
                $history->status_sebelumnya = $transaksi->listPengerjaan?->list_status_pengerjaan_id;
                $history->status_sesudahnya = $transaksi->listPengerjaan?->list_status_pengerjaan_id;
                $history->operator_id = auth()->id();
                $history->keterangan = "Inisiasi upgrade oleh operator: Layanan diubah ke {$newService->nama}. Selisih Rp " . number_format($priceDiff, 0, ',', '.') . " diterima tunai.";
                $history->save();
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $transaksi,
                    'message' => 'Layanan berhasil di-upgrade ke ' . $newService->nama . ' secara langsung.',
                    'status' => 200
                ], 200);
            }
            return redirect()->back()->with('success', 'Layanan berhasil di-upgrade ke ' . $newService->nama . ' secara langsung.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Gagal upgrade layanan: ' . $e->getMessage(),
                    'status' => 400
                ], 400);
            }
            return redirect()->back()->with('error', 'Gagal upgrade layanan: ' . $e->getMessage());
        }
    }
}
