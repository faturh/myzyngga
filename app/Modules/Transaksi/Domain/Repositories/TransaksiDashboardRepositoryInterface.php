<?php

namespace App\Modules\Transaksi\Domain\Repositories;

use App\Models\Cabang;
use App\Models\LayananPrioritas;
use App\Models\Transaksi;
use Illuminate\Support\Collection;

interface TransaksiDashboardRepositoryInterface
{
    public function getCabangByIdWithTrashed(int $id): ?Cabang;

    public function findCabangById(int $id): ?Cabang;

    public function getCabangBySlugWithTrashed(string $slug): ?Cabang;

    public function getCabangBySlug(string $slug): ?Cabang;

    public function getAllCabangWithTrashed(): Collection;

    public function getTransaksiByCabang(int $cabangId): Collection;

    public function getTransaksiByCabangAndPegawai(int $cabangId, int $pegawaiId): Collection;

    public function getJadwalByCabang(int $cabangId): Collection;

    public function getJadwalByCabangAndPegawai(int $cabangId, int $pegawaiId): Collection;

    public function getMonitoringByCabang(int $cabangId): Collection;

    public function findDetailCabangBySlugWithTrashed(string $slug): ?Cabang;

    public function findDetailCabangByIdWithTrashed(int $id): ?Cabang;

    public function findTransaksiDetailForCabang(int $cabangId, string $transaksiId): ?Transaksi;

    public function findTransaksiDetail(string $transaksiId): ?Transaksi;

    public function getDetailTransaksiItems(string $transaksiId): Collection;

    public function getLayananTambahanTransaksiItems(string $transaksiId): Collection;

    public function getPelangganOptions(): Collection;

    public function getGamisOptionsByCabang(int $cabangId): Collection;

    public function getJenisPakaianOptionsByCabang(int $cabangId): Collection;

    public function getLayananPrioritasOptionsByCabang(int $cabangId): Collection;

    public function getLayananTambahanOptionsByCabang(int $cabangId): Collection;

    public function getHargaLayananOptionsByCabang(int $cabangId): Collection;

    public function getJenisLayananOptionsByCabang(int $cabangId): Collection;

    public function getJenisLayananByJenisPakaian(int $cabangId, int $jenisPakaianId): Collection;

    public function getHargaLayananByJenis(int $cabangId, int $jenisPakaianId, int $jenisLayananId): ?object;

    public function getLayananTambahanById(int $cabangId, int $layananTambahanId): ?object;

    public function findLayananPrioritasByCabangAndId(int $cabangId, int $layananPrioritasId): ?LayananPrioritas;

    public function findTransaksiByCabang(int $cabangId, string $transaksiId): ?Transaksi;

    public function findTransaksiStatusByCabang(int $cabangId, string $transaksiId): ?Transaksi;

    public function updateTransaksiStatusByCabang(int $cabangId, string $transaksiId, string $status): int;

    public function updateKonfirmasiUpah(string $transaksiId, bool $konfirmasi): void;

    public function getTransaksiGamisByUser(int $cabangId, int $userId, ?string $tanggal = null): Collection;

    public function getMonitoringGamisByUser(int $cabangId, int $userId, ?string $tanggal = null): Collection;

    public function findTransaksiDetailForGamis(int $userId, string $transaksiId): ?Transaksi;

    public function storeTransaksiAggregate(array $transaksiPayload, array $detailGroups, array $layananTambahanIds): Transaksi;

    public function updateTransaksiAggregate(int $cabangId, string $transaksiId, array $transaksiPayload, array $detailGroups, array $layananTambahanIds): int;

    public function deleteTransaksiAggregate(int $cabangId, string $transaksiId): int;
}
