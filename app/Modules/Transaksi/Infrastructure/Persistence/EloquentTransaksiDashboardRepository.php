<?php

namespace App\Modules\Transaksi\Infrastructure\Persistence;

use App\Models\Cabang;
use App\Models\DetailLayananTransaksi;
use App\Models\DetailTransaksi;
use App\Models\HargaJenisLayanan;
use App\Models\JenisLayanan;
use App\Models\JenisPakaian;
use App\Models\LayananPrioritas;
use App\Models\LayananTambahan;
use App\Models\LayananTambahanTransaksi;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User;
use App\Modules\Transaksi\Domain\Repositories\TransaksiDashboardRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentTransaksiDashboardRepository implements TransaksiDashboardRepositoryInterface
{
    public function getCabangByIdWithTrashed(int $id): ?Cabang
    {
        return Cabang::withTrashed()->where('id', $id)->first();
    }

    public function findCabangById(int $id): ?Cabang
    {
        return Cabang::query()->where('id', $id)->first();
    }

    public function getCabangBySlugWithTrashed(string $slug): ?Cabang
    {
        return Cabang::withTrashed()->where('slug', $slug)->first();
    }

    public function getCabangBySlug(string $slug): ?Cabang
    {
        return Cabang::where('slug', $slug)->first();
    }

    public function getAllCabangWithTrashed(): Collection
    {
        return Cabang::withTrashed()->orderBy('created_at', 'asc')->get();
    }

    public function getTransaksiByCabang(int $cabangId): Collection
    {
        return Transaksi::query()
            ->with(['pegawai' => function ($query) {
                $query->withTrashed();
            }])
            ->with(['pelanggan:id,nama', 'layananPrioritas:id,nama'])
            ->where('cabang_id', $cabangId)
            ->orderBy('waktu', 'desc')
            ->get();
    }

    public function getTransaksiByCabangAndPegawai(int $cabangId, int $pegawaiId): Collection
    {
        return Transaksi::query()
            ->with(['pegawai' => function ($query) {
                $query->withTrashed();
            }])
            ->with(['pelanggan:id,nama', 'layananPrioritas:id,nama'])
            ->where('cabang_id', $cabangId)
            ->where('pegawai_id', $cabangId . '_' . $pegawaiId)
            ->orderBy('waktu', 'desc')
            ->get();
    }

    public function getJadwalByCabang(int $cabangId): Collection
    {
        return Transaksi::query()
            ->with(['pegawai' => function ($query) {
                $query->withTrashed();
            }])
            ->with(['pelanggan:id,nama', 'layananPrioritas:id,nama'])
            ->join('layanan_prioritas as lp', 'lp.id', '=', 'transaksi.layanan_prioritas_id')
            ->join('list_pengerjaan as lpen', 'lpen.id', '=', 'transaksi.list_pengerjaan_id')
            ->where('transaksi.cabang_id', $cabangId)
            ->where('lpen.list_status_pengerjaan_id', '!=', 5)
            ->where('lpen.list_status_pengerjaan_id', '!=', 7)
            ->orderBy('lp.prioritas', 'desc')
            ->orderBy('transaksi.waktu', 'asc')
            ->select('transaksi.*')
            ->get();
    }

    public function getJadwalByCabangAndPegawai(int $cabangId, int $pegawaiId): Collection
    {
        return Transaksi::query()
            ->with(['pegawai' => function ($query) {
                $query->withTrashed();
            }])
            ->with(['pelanggan:id,nama', 'layananPrioritas:id,nama'])
            ->join('layanan_prioritas as lp', 'lp.id', '=', 'transaksi.layanan_prioritas_id')
            ->join('list_pengerjaan as lpen', 'lpen.id', '=', 'transaksi.list_pengerjaan_id')
            ->where('transaksi.cabang_id', $cabangId)
            ->where('lpen.list_status_pengerjaan_id', '!=', 5)
            ->where('lpen.list_status_pengerjaan_id', '!=', 7)
            ->where('transaksi.pegawai_id', $cabangId . '_' . $pegawaiId)
            ->orderBy('lp.prioritas', 'desc')
            ->orderBy('transaksi.waktu', 'asc')
            ->select('transaksi.*')
            ->get();
    }

    public function getMonitoringByCabang(int $cabangId): Collection
    {
        return collect();
    }

    public function findDetailCabangBySlugWithTrashed(string $slug): ?Cabang
    {
        return Cabang::withTrashed()->where('slug', $slug)->first();
    }

    public function findDetailCabangByIdWithTrashed(int $id): ?Cabang
    {
        return Cabang::withTrashed()->where('id', $id)->first();
    }

    public function findTransaksiDetailForCabang(int $cabangId, string $transaksiId): ?Transaksi
    {
        return Transaksi::query()
            ->with(['pegawai' => function ($query) {
                $query->withTrashed();
            }])
            ->where('id', $transaksiId)
            ->where('cabang_id', $cabangId)
            ->orderBy('waktu', 'asc')
            ->first();
    }

    public function findTransaksiDetail(string $transaksiId): ?Transaksi
    {
        return Transaksi::query()
            ->with(['pegawai' => function ($query) {
                $query->withTrashed();
            }])
            ->where('id', $transaksiId)
            ->first();
    }

    public function getDetailTransaksiItems(string $transaksiId): Collection
    {
        return DetailTransaksi::where('transaksi_id', $transaksiId)->orderBy('id', 'asc')->get();
    }

    public function getLayananTambahanTransaksiItems(string $transaksiId): Collection
    {
        return LayananTambahanTransaksi::where('transaksi_id', $transaksiId)->orderBy('id', 'asc')->get();
    }

    public function getPelangganOptions(): Collection
    {
        return Pelanggan::query()->get();
    }

    public function getGamisOptionsByCabang(int $cabangId): Collection
    {
        return collect();
    }

    public function getJenisPakaianOptionsByCabang(int $cabangId): Collection
    {
        return JenisPakaian::all();
    }

    public function getLayananPrioritasOptionsByCabang(int $cabangId): Collection
    {
        return LayananPrioritas::where('cabang_id', $cabangId)->get();
    }

    public function getLayananTambahanOptionsByCabang(int $cabangId): Collection
    {
        return LayananTambahan::where('cabang_id', $cabangId)->get();
    }

    public function getHargaLayananOptionsByCabang(int $cabangId): Collection
    {
        return HargaJenisLayanan::where('cabang_id', $cabangId)->get();
    }

    public function getJenisLayananOptionsByCabang(int $cabangId): Collection
    {
        return JenisLayanan::where('cabang_id', $cabangId)->get();
    }

    public function getJenisLayananByJenisPakaian(int $cabangId, int $jenisPakaianId): Collection
    {
        return HargaJenisLayanan::query()
            ->join('jenis_layanan as jl', 'harga_jenis_layanan.jenis_layanan_id', '=', 'jl.id')
            ->where('harga_jenis_layanan.cabang_id', $cabangId)
            ->where('harga_jenis_layanan.jenis_pakaian_id', $jenisPakaianId)
            ->select('jl.id', 'jl.nama')
            ->get();
    }

    public function getHargaLayananByJenis(int $cabangId, int $jenisPakaianId, int $jenisLayananId): ?object
    {
        return HargaJenisLayanan::query()
            ->where('cabang_id', $cabangId)
            ->where('jenis_pakaian_id', $jenisPakaianId)
            ->where('jenis_layanan_id', $jenisLayananId)
            ->first();
    }

    public function getLayananTambahanById(int $cabangId, int $layananTambahanId): ?object
    {
        return LayananTambahan::query()
            ->where('cabang_id', $cabangId)
            ->where('id', $layananTambahanId)
            ->first();
    }

    public function findLayananPrioritasByCabangAndId(int $cabangId, int $layananPrioritasId): ?LayananPrioritas
    {
        return LayananPrioritas::query()
            ->where('cabang_id', $cabangId)
            ->where('id', $layananPrioritasId)
            ->first();
    }

    public function findTransaksiByCabang(int $cabangId, string $transaksiId): ?Transaksi
    {
        return Transaksi::where('cabang_id', $cabangId)->where('id', $transaksiId)->first();
    }

    public function findTransaksiStatusByCabang(int $cabangId, string $transaksiId): ?Transaksi
    {
        return Transaksi::where('cabang_id', $cabangId)->where('id', $transaksiId)->first(['id', 'status']);
    }

    public function updateTransaksiStatusByCabang(int $cabangId, string $transaksiId, string $status): int
    {
        $transaksi = Transaksi::where('cabang_id', $cabangId)->where('id', $transaksiId)->first();
        if ($transaksi) {
            $transaksi->status = $status;
            $transaksi->save();
            return 1;
        }
        return 0;
    }

    public function updateKonfirmasiUpah(string $transaksiId, bool $konfirmasi): void
    {
        // No-op
    }

    public function getTransaksiGamisByUser(int $cabangId, int $userId, ?string $tanggal = null): Collection
    {
        return collect();
    }

    public function getMonitoringGamisByUser(int $cabangId, int $userId, ?string $tanggal = null): Collection
    {
        return collect();
    }

    public function findTransaksiDetailForGamis(int $userId, string $transaksiId): ?Transaksi
    {
        return null;
    }

    public function storeTransaksiAggregate(array $transaksiPayload, array $detailGroups, array $layananTambahanIds): Transaksi
    {
        return DB::transaction(function () use ($transaksiPayload, $detailGroups, $layananTambahanIds) {
            $transaksi = Transaksi::query()->create($transaksiPayload);

            foreach ($detailGroups as $detailGroup) {
                $detailTransaksi = DetailTransaksi::query()->create([
                    'total_pakaian' => $detailGroup['total_pakaian'],
                    'harga_layanan_akhir' => $detailGroup['harga_layanan_akhir'],
                    'total_biaya_layanan' => $detailGroup['total_biaya_layanan'],
                    'total_biaya_prioritas' => $detailGroup['total_biaya_prioritas'],
                    'transaksi_id' => $transaksi->id,
                ]);

                foreach ($detailGroup['harga_jenis_layanan_ids'] as $hargaJenisLayananId) {
                    DetailLayananTransaksi::query()->create([
                        'harga_jenis_layanan_id' => $hargaJenisLayananId,
                        'detail_transaksi_id' => $detailTransaksi->id,
                    ]);
                }
            }

            foreach ($layananTambahanIds as $layananTambahanId) {
                LayananTambahanTransaksi::query()->create([
                    'layanan_tambahan_id' => $layananTambahanId,
                    'transaksi_id' => $transaksi->id,
                ]);
            }

            return $transaksi->refresh();
        });
    }

    public function updateTransaksiAggregate(int $cabangId, string $transaksiId, array $transaksiPayload, array $detailGroups, array $layananTambahanIds): int
    {
        return DB::transaction(function () use ($cabangId, $transaksiId, $transaksiPayload, $detailGroups, $layananTambahanIds) {
            $transaksi = Transaksi::where('cabang_id', $cabangId)->where('id', $transaksiId)->first();
            if ($transaksi) {
                $transaksi->fill($transaksiPayload);
                $transaksi->save();
                $updated = 1;
            } else {
                $updated = 0;
            }

            $detailIds = DetailTransaksi::query()
                ->where('transaksi_id', $transaksiId)
                ->pluck('id')
                ->all();

            if ($detailIds !== []) {
                DetailLayananTransaksi::query()->whereIn('detail_transaksi_id', $detailIds)->delete();
            }

            DetailTransaksi::query()->where('transaksi_id', $transaksiId)->delete();
            LayananTambahanTransaksi::query()->where('transaksi_id', $transaksiId)->delete();

            foreach ($detailGroups as $detailGroup) {
                $detailTransaksi = DetailTransaksi::query()->create([
                    'total_pakaian' => $detailGroup['total_pakaian'],
                    'harga_layanan_akhir' => $detailGroup['harga_layanan_akhir'],
                    'total_biaya_layanan' => $detailGroup['total_biaya_layanan'],
                    'total_biaya_prioritas' => $detailGroup['total_biaya_prioritas'],
                    'transaksi_id' => $transaksiId,
                ]);

                foreach ($detailGroup['harga_jenis_layanan_ids'] as $hargaJenisLayananId) {
                    DetailLayananTransaksi::query()->create([
                        'harga_jenis_layanan_id' => $hargaJenisLayananId,
                        'detail_transaksi_id' => $detailTransaksi->id,
                    ]);
                }
            }

            foreach ($layananTambahanIds as $layananTambahanId) {
                LayananTambahanTransaksi::query()->create([
                    'layanan_tambahan_id' => $layananTambahanId,
                    'transaksi_id' => $transaksiId,
                ]);
            }

            return $updated;
        });
    }

    public function deleteTransaksiAggregate(int $cabangId, string $transaksiId): int
    {
        return DB::transaction(function () use ($cabangId, $transaksiId) {
            $detailIds = DetailTransaksi::query()
                ->where('transaksi_id', $transaksiId)
                ->pluck('id')
                ->all();

            if ($detailIds !== []) {
                DetailLayananTransaksi::query()->whereIn('detail_transaksi_id', $detailIds)->delete();
            }

            DetailTransaksi::query()->where('transaksi_id', $transaksiId)->delete();
            LayananTambahanTransaksi::query()->where('transaksi_id', $transaksiId)->delete();

            return Transaksi::query()
                ->where('cabang_id', $cabangId)
                ->where('id', $transaksiId)
                ->delete();
        });
    }
}
