<?php

namespace App\Modules\Transaksi\Application\Services;

use App\Enums\JenisPembayaran;
use App\Enums\StatusTransaksi;
use App\Models\DetailLayananTransaksi;
use App\Models\DetailTransaksi;
use App\Models\HargaJenisLayanan;
use App\Models\JenisLayanan;
use App\Models\JenisPakaian;
use App\Models\LayananPrioritas;
use App\Models\LayananTambahanTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use App\Modules\Transaksi\Domain\Repositories\TransaksiDashboardRepositoryInterface;
use App\Shared\Exceptions\DomainException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiDashboardService
{
    public function __construct(
        private readonly TransaksiDashboardRepositoryInterface $repository,
    ) {}

    public function indexData(User $user): array
    {
        $title = 'Transaksi Layanan';
        $userRole = $user->roles[0]->name;

        if ($userRole === 'lurah' || $userRole === 'pic') {
            return [
                'view' => 'dashboard.transaksi.lurah.index',
                'data' => [
                    'title' => $title,
                    'cabang' => $this->repository->getAllCabangWithTrashed(),
                ],
            ];
        }

        $cabang = $this->repository->getCabangByIdWithTrashed((int) $user->cabang_id);
        $status = StatusTransaksi::cases();
        $isJadwal = false;

        if (! $cabang) {
            throw new DomainException('Cabang user tidak ditemukan.', 404);
        }

        if ($userRole === 'manajer_laundry') {
            $transaksi = $this->repository->getTransaksiByCabang((int) $cabang->id);
            $monitoring = $this->repository->getMonitoringByCabang((int) $cabang->id);
        } elseif ($userRole === 'pegawai_laundry') {
            $transaksi = $this->repository->getTransaksiByCabangAndPegawai((int) $cabang->id, (int) $user->id);
            $monitoring = collect();
        } else {
            throw new DomainException('Role tidak diizinkan mengakses data transaksi.', 403);
        }

        return [
            'view' => 'dashboard.transaksi.index',
            'data' => compact('title', 'cabang', 'transaksi', 'monitoring', 'isJadwal', 'status'),
        ];
    }

    public function jadwalData(User $user): array
    {
        $title = 'Transaksi Layanan';
        $userRole = $user->roles[0]->name;
        $status = StatusTransaksi::cases();
        $isJadwal = true;

        if (in_array($userRole, ['lurah', 'pic', 'rw', 'gamis'], true)) {
            throw new DomainException('USER DOES NOT HAVE THE RIGHT ROLES.', 403);
        }

        $cabang = $this->repository->getCabangByIdWithTrashed((int) $user->cabang_id);
        if (! $cabang || $cabang->deleted_at) {
            throw new DomainException('CABANG TIDAK DITEMUKAN ATAU SUDAH DIHAPUS.', 404);
        }

        if ($userRole === 'manajer_laundry') {
            $transaksi = $this->repository->getJadwalByCabang((int) $cabang->id);
        } else {
            $transaksi = $this->repository->getJadwalByCabangAndPegawai((int) $cabang->id, (int) $user->id);
        }

        return [
            'view' => 'dashboard.transaksi.jadwal',
            'data' => compact('title', 'cabang', 'transaksi', 'isJadwal', 'status'),
        ];
    }

    public function cabangData(User $user, string $slug): array
    {
        $title = 'Transaksi Layanan';
        $userRole = $user->roles[0]->name;
        $isJadwal = false;
        $status = StatusTransaksi::cases();

        if ($userRole !== 'lurah' && $userRole !== 'pic') {
            throw new DomainException('USER DOES NOT HAVE THE RIGHT ROLES.', 403);
        }

        $cabang = $this->repository->getCabangBySlugWithTrashed($slug);
        if (! $cabang) {
            throw new DomainException('CABANG TIDAK DITEMUKAN ATAU SUDAH DIHAPUS.', 404);
        }

        $transaksi = $this->repository->getTransaksiByCabang((int) $cabang->id);
        $monitoring = $this->repository->getMonitoringByCabang((int) $cabang->id);

        return [
            'view' => 'dashboard.transaksi.lurah.cabang',
            'data' => compact('title', 'cabang', 'transaksi', 'monitoring', 'isJadwal', 'status'),
        ];
    }

    public function cabangJadwalData(User $user, string $slug): array
    {
        $title = 'Jadwal Transaksi Layanan';
        $userRole = $user->roles[0]->name;
        $isJadwal = true;
        $status = StatusTransaksi::cases();

        if ($userRole !== 'lurah' && $userRole !== 'pic') {
            throw new DomainException('USER DOES NOT HAVE THE RIGHT ROLES.', 403);
        }

        $cabang = $this->repository->getCabangBySlug($slug);
        if (! $cabang || $cabang->deleted_at) {
            throw new DomainException('CABANG TIDAK DITEMUKAN ATAU SUDAH DIHAPUS.', 404);
        }

        $transaksi = $this->repository->getJadwalByCabang((int) $cabang->id);

        return [
            'view' => 'dashboard.transaksi.lurah.jadwal',
            'data' => compact('title', 'cabang', 'transaksi', 'isJadwal', 'status'),
        ];
    }

    public function viewDetailTransaksiData(User $user, Request $request): array
    {
        $title = 'Transaksi Layanan';
        $userRole = $user->roles[0]->name;
        $isJadwal = $request->isJadwal;

        if ($userRole === 'lurah' || $userRole === 'pic') {
            $cabang = $this->repository->findDetailCabangBySlugWithTrashed((string) $request->cabang);
            $transaksi = $this->repository->findTransaksiDetailForCabang((int) $cabang->id, (string) $request->transaksi);
            $detailTransaksi = $this->repository->getDetailTransaksiItems((string) $transaksi->id);
            $layananTambahanTransaksi = $this->repository->getLayananTambahanTransaksiItems((string) $transaksi->id);

            return [
                'view' => 'dashboard.transaksi.lurah.lihat',
                'data' => compact('title', 'cabang', 'transaksi', 'detailTransaksi', 'isJadwal', 'layananTambahanTransaksi'),
            ];
        }

        $cabang = $this->repository->findDetailCabangByIdWithTrashed((int) $user->cabang_id);
        $transaksi = $this->repository->findTransaksiDetail((string) $request->transaksi);
        $detailTransaksi = $this->repository->getDetailTransaksiItems((string) $transaksi->id);
        $layananTambahanTransaksi = $this->repository->getLayananTambahanTransaksiItems((string) $transaksi->id);

        return [
            'view' => 'dashboard.transaksi.lihat',
            'data' => compact('title', 'cabang', 'transaksi', 'detailTransaksi', 'isJadwal', 'layananTambahanTransaksi'),
        ];
    }

    public function createTransaksiCabangData(User $user, Request $request): array
    {
        $title = 'Tambah Transaksi';
        $userRole = $user->roles[0]->name;
        $isJadwal = $request->isJadwal;
        $jenisPembayaran = JenisPembayaran::cases();

        if ($userRole === 'lurah' || $userRole === 'pic') {
            throw new DomainException('USER DOES NOT HAVE THE RIGHT ROLES.', 403);
        }

        $cabang = $this->repository->findDetailCabangByIdWithTrashed((int) $user->cabang_id);
        if ($cabang->deleted_at) {
            throw new DomainException('FITUR TIDAK DAPAT DIGUNAKAN.', 404);
        }

        $pelanggan = $this->repository->getPelangganOptions();
        $gamis = $this->repository->getGamisOptionsByCabang((int) $cabang->id);
        $pakaian = $this->repository->getJenisPakaianOptionsByCabang((int) $cabang->id);
        $layananPrioritas = $this->repository->getLayananPrioritasOptionsByCabang((int) $cabang->id);
        $layananTambahan = $this->repository->getLayananTambahanOptionsByCabang((int) $cabang->id);

        return [
            'view' => 'dashboard.transaksi.tambah',
            'data' => compact('title', 'cabang', 'pelanggan', 'gamis', 'pakaian', 'layananPrioritas', 'isJadwal', 'jenisPembayaran', 'layananTambahan'),
        ];
    }

    public function storeTransaksiCabang(User $user, Request $request): Transaksi
    {
        $cabang = Cabang::where('id', $user->cabang_id)->first();

        $validatorTransaksi = Validator::make($request->all(), [
            'total_biaya_layanan' => 'required|decimal:0,2',
            'total_biaya_prioritas' => 'required|decimal:0,2',
            'total_biaya_layanan_tambahan' => 'required|decimal:0,2',
            'total_bayar_akhir' => 'required|decimal:0,2',
            'jenis_pembayaran' => 'required|string|max:255',
            'bayar' => 'required|decimal:0,2',
            'kembalian' => 'required|decimal:0,2',
            'layanan_prioritas_id' => 'required|integer',
            'pelanggan_id' => 'required|integer',
            'gamis_id' => 'nullable|integer',
        ]);

        $validatedTransaksi = $validatorTransaksi->validated();
        $validatedTransaksi['cabang_id'] = $cabang->id;
        $validatedTransaksi['pegawai_id'] = $user->id;
        $validatedTransaksi['waktu'] = Carbon::now();
        $nota = Carbon::now()->format('His').'-'.Carbon::now()->format('dmY').'-'.$cabang->id.$request->pelanggan_id;
        $validatedTransaksi['nota_layanan'] = 'layanan-'.$nota;
        $validatedTransaksi['nota_pelanggan'] = 'pelanggan-'.$nota;
        $validatedTransaksi['status'] = StatusTransaksi::BARU->value;

        return DB::transaction(function () use ($request, $validatedTransaksi, $cabang) {
            $transaksi = Transaksi::create($validatedTransaksi);
            $layananPrioritas = LayananPrioritas::where('cabang_id', $cabang->id)->where('id', $request->layanan_prioritas_id)->first();

            foreach ($request->jenis_pakaian_id as $item => $value) {
                $detailTransaksi = DetailTransaksi::create([
                    'total_pakaian' => $request->total_pakaian[$item],
                    'harga_layanan_akhir' => $request->harga_jenis_layanan_id[$item],
                    'total_biaya_layanan' => $request->total_pakaian[$item] * $request->harga_jenis_layanan_id[$item],
                    'total_biaya_prioritas' => $request->total_pakaian[$item] * $layananPrioritas->harga,
                    'transaksi_id' => $transaksi->id,
                ]);

                foreach ($request->jenis_layanan_id[$item] as $layanan) {
                    $jenisPakaian = JenisPakaian::where('cabang_id', $cabang->id)->where('id', $value)->first();
                    $jenisLayanan = JenisLayanan::where('cabang_id', $cabang->id)->where('id', $layanan)->first();
                    $hargaLayanan = HargaJenisLayanan::where('cabang_id', $cabang->id)
                        ->where('jenis_pakaian_id', $jenisPakaian->id)
                        ->where('jenis_layanan_id', $jenisLayanan->id)
                        ->first();
                    DetailLayananTransaksi::create([
                        'harga_jenis_layanan_id' => $hargaLayanan->id,
                        'detail_transaksi_id' => $detailTransaksi->id,
                    ]);
                }
            }

            if ($request->layanan_tambahan_id) {
                foreach ($request->layanan_tambahan_id as $item) {
                    LayananTambahanTransaksi::create([
                        'layanan_tambahan_id' => $item,
                        'transaksi_id' => $transaksi->id,
                    ]);
                }
            }

            return $transaksi;
        });
    }

    public function editTransaksiCabangData(User $user, Request $request): array
    {
        $title = 'Ubah Transaksi';
        $isJadwal = $request->isJadwal;
        $status = StatusTransaksi::cases();
        $jenisPembayaran = JenisPembayaran::cases();

        $cabang = $this->repository->findDetailCabangByIdWithTrashed((int) $user->cabang_id);
        if ($cabang->deleted_at) {
            throw new DomainException('FITUR TIDAK DAPAT DIGUNAKAN.', 404);
        }

        $pelanggan = $this->repository->getPelangganOptions();
        $gamis = $this->repository->getGamisOptionsByCabang((int) $cabang->id);
        $pakaian = $this->repository->getJenisPakaianOptionsByCabang((int) $cabang->id);
        $layananPrioritas = $this->repository->getLayananPrioritasOptionsByCabang((int) $cabang->id);
        $layananTambahan = $this->repository->getLayananTambahanOptionsByCabang((int) $cabang->id);
        $transaksi = $this->repository->findTransaksiByCabang((int) $cabang->id, (string) $request->transaksi);
        $hargaLayanan = $this->repository->getHargaLayananOptionsByCabang((int) $cabang->id);
        $layanan = $this->repository->getJenisLayananOptionsByCabang((int) $cabang->id);

        if ($transaksi->status === 'Selesai' && $user->roles[0]->name === 'pegawai_laundry') {
            throw new DomainException('Transaksi Ini Tidak Dapat Diubah', 403);
        }

        return [
            'view' => 'dashboard.transaksi.ubah',
            'data' => compact('title', 'cabang', 'status', 'pelanggan', 'gamis', 'pakaian', 'layananPrioritas', 'transaksi', 'layanan', 'hargaLayanan', 'isJadwal', 'jenisPembayaran', 'layananTambahan'),
        ];
    }

    public function updateTransaksiCabang(User $user, Request $request): int
    {
        $cabang = Cabang::where('id', $user->cabang_id)->first();
        $getTransaksi = Transaksi::where('cabang_id', $cabang->id)->where('id', $request->transaksi)->first();

        $validatorTransaksi = Validator::make($request->all(), [
            'total_biaya_layanan' => 'required|decimal:0,2',
            'total_biaya_prioritas' => 'required|decimal:0,2',
            'total_biaya_layanan_tambahan' => 'required|decimal:0,2',
            'total_bayar_akhir' => 'required|decimal:0,2',
            'jenis_pembayaran' => 'required|string|max:255',
            'bayar' => 'required|decimal:0,2',
            'kembalian' => 'required|decimal:0,2',
            'status' => 'required|string',
            'layanan_prioritas_id' => 'required|integer',
            'pelanggan_id' => 'required|integer',
            'gamis_id' => 'nullable|integer',
        ]);

        $validatedTransaksi = $validatorTransaksi->validated();

        return DB::transaction(function () use ($request, $cabang, $getTransaksi, $validatedTransaksi) {
            $transaksi = Transaksi::where('cabang_id', $cabang->id)->where('id', $getTransaksi->id)->update($validatedTransaksi);
            $layananPrioritas = LayananPrioritas::where('cabang_id', $cabang->id)->where('id', $request->layanan_prioritas_id)->first();

            $detailTransaksi = DetailTransaksi::where('transaksi_id', $getTransaksi->id)->get();
            foreach ($detailTransaksi as $item) {
                DetailLayananTransaksi::where('detail_transaksi_id', $item->id)->delete();
            }
            DetailTransaksi::where('transaksi_id', $getTransaksi->id)->delete();

            foreach ($request->jenis_pakaian_id as $item => $value) {
                $detail = DetailTransaksi::create([
                    'total_pakaian' => $request->total_pakaian[$item],
                    'harga_layanan_akhir' => $request->harga_jenis_layanan_id[$item],
                    'total_biaya_layanan' => $request->total_pakaian[$item] * $request->harga_jenis_layanan_id[$item],
                    'total_biaya_prioritas' => $request->total_pakaian[$item] * $layananPrioritas->harga,
                    'transaksi_id' => $getTransaksi->id,
                ]);

                foreach ($request->jenis_layanan_id[$item] as $layanan) {
                    $jenisPakaian = JenisPakaian::where('cabang_id', $cabang->id)->where('id', $value)->first();
                    $jenisLayanan = JenisLayanan::where('cabang_id', $cabang->id)->where('id', $layanan)->first();
                    $hargaLayanan = HargaJenisLayanan::where('cabang_id', $cabang->id)
                        ->where('jenis_pakaian_id', $jenisPakaian->id)
                        ->where('jenis_layanan_id', $jenisLayanan->id)
                        ->first();
                    DetailLayananTransaksi::create([
                        'harga_jenis_layanan_id' => $hargaLayanan->id,
                        'detail_transaksi_id' => $detail->id,
                    ]);
                }
            }

            LayananTambahanTransaksi::where('transaksi_id', $getTransaksi->id)->delete();
            if ($request->layanan_tambahan_id) {
                foreach ($request->layanan_tambahan_id as $item) {
                    LayananTambahanTransaksi::create([
                        'layanan_tambahan_id' => $item,
                        'transaksi_id' => $getTransaksi->id,
                    ]);
                }
            }

            return $transaksi;
        });
    }

    public function editStatusTransaksiCabang(User $user, Request $request): ?Transaksi
    {
        return $this->repository->findTransaksiStatusByCabang((int) $user->cabang_id, (string) $request->transaksi_id);
    }

    public function updateStatusTransaksiCabang(User $user, Request $request)
    {
        $perbarui = $this->repository->updateTransaksiStatusByCabang((int) $user->cabang_id, (string) $request->id, (string) $request->status);

        if ($request->isJadwal) {
            return $perbarui
                ? to_route('transaksi.jadwal')->with('success', 'Status Transaksi Berhasil Diperbarui')
                : to_route('transaksi.jadwal')->with('error', 'Status Transaksi Gagal Diperbarui');
        }

        return $perbarui
            ? to_route('transaksi')->with('success', 'Status Transaksi Berhasil Diperbarui')
            : to_route('transaksi')->with('error', 'Status Transaksi Gagal Diperbarui');
    }

    public function deleteTransaksiCabang(User $user, Request $request): void
    {
        $cabang = Cabang::where('id', $user->cabang_id)->first();
        $getTransaksi = Transaksi::where('cabang_id', $cabang->id)->where('id', $request->transaksi_id)->first();

        $detailTransaksi = DetailTransaksi::where('transaksi_id', $getTransaksi->id)->get();
        foreach ($detailTransaksi as $item) {
            DetailLayananTransaksi::where('detail_transaksi_id', $item->id)->delete();
        }
        DetailTransaksi::where('transaksi_id', $getTransaksi->id)->delete();
        $hapus = Transaksi::where('id', $request->transaksi_id)->delete();

        if (! $hapus) {
            throw new DomainException('Transaksi Gagal Dihapus', 400);
        }
    }

    public function ubahJenisPakaian(User $user, Request $request)
    {
        return $this->repository->getJenisLayananByJenisPakaian((int) $user->cabang_id, (int) $request->jenisPakaianId);
    }

    public function ubahJenisLayanan(User $user, Request $request): float|int
    {
        $hargaLayananAkhir = 0;

        foreach ($request->jenisLayananId as $item) {
            $hargaLayanan = $this->repository->getHargaLayananByJenis((int) $user->cabang_id, (int) $request->jenisPakaianId, (int) $item);
            $hargaLayananAkhir += $hargaLayanan?->harga ?? 0;
        }

        return $hargaLayananAkhir;
    }

    public function ubahLayananTambahan(User $user, Request $request): float|int
    {
        $hargaLayananAkhir = 0;

        foreach ($request->layananTambahanId as $item) {
            $hargaLayanan = $this->repository->getLayananTambahanById((int) $user->cabang_id, (int) $item);
            $hargaLayananAkhir += $hargaLayanan?->harga ?? 0;
        }

        return $hargaLayananAkhir;
    }

    public function hitungTotalBayar(User $user, Request $request): array
    {
        $cabang = Cabang::where('id', $user->cabang_id)->first();
        $hargaLayananId = $request->hargaLayanan;
        $totalPakaian = $request->totalPakaian;
        $layananPrioritas = LayananPrioritas::where('cabang_id', $cabang->id)->where('id', $request->layananPrioritas)->first();

        $biayaLayanan = 0;
        $biayaPrioritas = 0;
        foreach ($hargaLayananId as $item => $value) {
            $biayaLayanan += $value * $totalPakaian[$item];
            $biayaPrioritas += $layananPrioritas->harga * $totalPakaian[$item];
        }
        $totalBayar = $biayaLayanan + $biayaPrioritas + $request->layananTambahan;

        return [$biayaLayanan, $biayaPrioritas, $totalBayar];
    }

    public function cetakStrukTransaksiData(Request $request): array
    {
        $title = 'Cetak Struk';
        $transaksi = $this->repository->findTransaksiDetail((string) $request->transaksi);
        $detailTransaksi = $this->repository->getDetailTransaksiItems((string) $transaksi->id);
        $layananTambahanTransaksi = $this->repository->getLayananTambahanTransaksiItems((string) $transaksi->id);
        $cabang = $this->repository->getCabangByIdWithTrashed((int) $transaksi->cabang_id);

        return compact('title', 'transaksi', 'detailTransaksi', 'cabang', 'layananTambahanTransaksi');
    }

    public function konfirmasiUpah(Request $request): void
    {
        $this->repository->updateKonfirmasiUpah((string) $request->transaksi_id, ! $request->boolean('konfirmasi'));
    }

    public function transaksiGamisData(User $user, bool $harian): array
    {
        $title = $harian ? 'Transaksi Gamis Harian' : 'Transaksi Gamis';
        $isHarian = $harian;
        $userRole = $user->roles[0]->name;

        if ($userRole !== 'gamis') {
            throw new DomainException('USER DOES NOT HAVE THE RIGHT ROLES.', 403);
        }

        $cabang = $this->repository->findDetailCabangByIdWithTrashed((int) $user->cabang_id);
        if ($cabang == null) {
            throw new DomainException('CABANG TIDAK DITEMUKAN ATAU SUDAH DIHAPUS.', 404);
        }

        $tanggal = $harian ? Carbon::now()->format('Y-m-d') : null;
        $transaksi = $this->repository->getTransaksiGamisByUser((int) $cabang->id, (int) $user->id, $tanggal);
        $monitoring = $this->repository->getMonitoringGamisByUser((int) $cabang->id, (int) $user->id, $tanggal);

        return compact('title', 'cabang', 'transaksi', 'monitoring', 'isHarian');
    }

    public function viewDetailTransaksiGamisData(User $user, Request $request): array
    {
        $title = 'Transaksi Layanan';
        $isHarian = $request->isHarian;
        $cabang = $this->repository->findDetailCabangByIdWithTrashed((int) $user->cabang_id);
        $transaksi = $this->repository->findTransaksiDetailForGamis((int) $user->id, (string) $request->transaksi);
        $detailTransaksi = $this->repository->getDetailTransaksiItems((string) $transaksi->id);
        $layananTambahanTransaksi = $this->repository->getLayananTambahanTransaksiItems((string) $transaksi->id);

        return compact('title', 'cabang', 'transaksi', 'detailTransaksi', 'layananTambahanTransaksi', 'isHarian');
    }
}
