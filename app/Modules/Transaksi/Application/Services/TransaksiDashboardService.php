<?php

namespace App\Modules\Transaksi\Application\Services;

use App\Enums\JenisPembayaran;
use App\Enums\StatusTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use App\Modules\Transaksi\Application\DTO\UpsertTransaksiData;
use App\Modules\Transaksi\Domain\Repositories\TransaksiDashboardRepositoryInterface;
use App\Shared\Exceptions\DomainException;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
                'view' => 'operator.dashboard.transaksi.lurah.index',
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
            'view' => 'operator.dashboard.transaksi.index',
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
            'view' => 'operator.dashboard.transaksi.jadwal',
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
            'view' => 'operator.dashboard.transaksi.lurah.cabang',
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
            'view' => 'operator.dashboard.transaksi.lurah.jadwal',
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
                'view' => 'operator.dashboard.transaksi.lurah.lihat',
                'data' => compact('title', 'cabang', 'transaksi', 'detailTransaksi', 'isJadwal', 'layananTambahanTransaksi'),
            ];
        }

        $cabang = $this->repository->findDetailCabangByIdWithTrashed((int) $user->cabang_id);
        $transaksi = $this->repository->findTransaksiDetail((string) $request->transaksi);
        $detailTransaksi = $this->repository->getDetailTransaksiItems((string) $transaksi->id);
        $layananTambahanTransaksi = $this->repository->getLayananTambahanTransaksiItems((string) $transaksi->id);

        return [
            'view' => 'operator.dashboard.transaksi.lihat',
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
            'view' => 'operator.dashboard.transaksi.tambah',
            'data' => compact('title', 'cabang', 'pelanggan', 'gamis', 'pakaian', 'layananPrioritas', 'isJadwal', 'jenisPembayaran', 'layananTambahan'),
        ];
    }

    public function storeTransaksiCabang(User $user, UpsertTransaksiData $data): Transaksi
    {
        $cabang = $this->repository->findCabangById((int) $user->cabang_id);
        if (! $cabang) {
            throw new DomainException('Cabang user tidak ditemukan.', 404);
        }

        $layananPrioritas = $this->repository->findLayananPrioritasByCabangAndId((int) $cabang->id, $data->layananPrioritasId);
        if (! $layananPrioritas) {
            throw new DomainException('Layanan prioritas tidak ditemukan di cabang ini.', 404);
        }

        return $this->repository->storeTransaksiAggregate(
            $data->toStorePayload((int) $cabang->id, (int) $user->id),
            $this->buildDetailGroups((int) $cabang->id, $data, (float) $layananPrioritas->harga),
            $data->layananTambahanIds,
        );
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
            'view' => 'operator.dashboard.transaksi.ubah',
            'data' => compact('title', 'cabang', 'status', 'pelanggan', 'gamis', 'pakaian', 'layananPrioritas', 'transaksi', 'layanan', 'hargaLayanan', 'isJadwal', 'jenisPembayaran', 'layananTambahan'),
        ];
    }

    public function updateTransaksiCabang(User $user, string $transaksiId, UpsertTransaksiData $data): int
    {
        $cabang = $this->repository->findCabangById((int) $user->cabang_id);
        if (! $cabang) {
            throw new DomainException('Cabang user tidak ditemukan.', 404);
        }

        $transaksi = $this->repository->findTransaksiByCabang((int) $cabang->id, $transaksiId);
        if (! $transaksi) {
            throw new DomainException('Transaksi tidak ditemukan.', 404);
        }

        $layananPrioritas = $this->repository->findLayananPrioritasByCabangAndId((int) $cabang->id, $data->layananPrioritasId);
        if (! $layananPrioritas) {
            throw new DomainException('Layanan prioritas tidak ditemukan di cabang ini.', 404);
        }

        return $this->repository->updateTransaksiAggregate(
            (int) $cabang->id,
            $transaksiId,
            $data->toUpdatePayload(),
            $this->buildDetailGroups((int) $cabang->id, $data, (float) $layananPrioritas->harga),
            $data->layananTambahanIds,
        );
    }

    public function editStatusTransaksiCabang(User $user, Request $request): ?Transaksi
    {
        return $this->repository->findTransaksiStatusByCabang((int) $user->cabang_id, (string) $request->transaksi_id);
    }

    public function updateStatusTransaksiCabang(User $user, string $transaksiId, string $status): int
    {
        return $this->repository->updateTransaksiStatusByCabang((int) $user->cabang_id, $transaksiId, $status);
    }

    public function deleteTransaksiCabang(User $user, string $transaksiId): void
    {
        $hapus = $this->repository->deleteTransaksiAggregate((int) $user->cabang_id, $transaksiId);

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
        $hargaLayananId = $request->hargaLayanan;
        $totalPakaian = $request->totalPakaian;
        $layananPrioritas = $this->repository->findLayananPrioritasByCabangAndId((int) $user->cabang_id, (int) $request->layananPrioritas);
        if (! $layananPrioritas) {
            throw new DomainException('Layanan prioritas tidak ditemukan di cabang ini.', 404);
        }

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

    private function buildDetailGroups(int $cabangId, UpsertTransaksiData $data, float $hargaPrioritas): array
    {
        $detailGroups = [];

        foreach ($data->lineItems as $lineItem) {
            $hargaJenisLayananIds = [];

            foreach ($lineItem->jenisLayananIds as $jenisLayananId) {
                $hargaLayanan = $this->repository->getHargaLayananByJenis($cabangId, $lineItem->jenisPakaianId, $jenisLayananId);
                if (! $hargaLayanan) {
                    throw new DomainException('Harga layanan untuk kombinasi pakaian dan layanan tidak ditemukan.', 404);
                }

                $hargaJenisLayananIds[] = (int) $hargaLayanan->id;
            }

            $detailGroups[] = [
                'total_pakaian' => $lineItem->totalPakaian,
                'harga_layanan_akhir' => $lineItem->hargaLayananAkhir,
                'total_biaya_layanan' => $lineItem->totalPakaian * $lineItem->hargaLayananAkhir,
                'total_biaya_prioritas' => $lineItem->totalPakaian * $hargaPrioritas,
                'harga_jenis_layanan_ids' => $hargaJenisLayananIds,
            ];
        }

        return $detailGroups;
    }
}

