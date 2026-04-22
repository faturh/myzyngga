<?php

namespace App\Modules\Transaksi\Infrastructure\Persistence;

use App\Models\Cabang;
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
            ->with(['pelanggan:id,nama', 'layananPrioritas:id,nama', 'gamis:id,nama'])
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
            ->with(['pelanggan:id,nama', 'layananPrioritas:id,nama', 'gamis:id,nama'])
            ->where('cabang_id', $cabangId)
            ->where('pegawai_id', $pegawaiId)
            ->orderBy('waktu', 'desc')
            ->get();
    }

    public function getJadwalByCabang(int $cabangId): Collection
    {
        return Transaksi::query()
            ->with(['pegawai' => function ($query) {
                $query->withTrashed();
            }])
            ->with(['pelanggan:id,nama', 'layananPrioritas:id,nama', 'gamis:id,nama'])
            ->join('layanan_prioritas as lp', 'lp.id', '=', 'transaksi.layanan_prioritas_id')
            ->where('transaksi.cabang_id', $cabangId)
            ->where('transaksi.status', '!=', 'Selesai')
            ->where('transaksi.status', '!=', 'Batal')
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
            ->with(['pelanggan:id,nama', 'layananPrioritas:id,nama', 'gamis:id,nama'])
            ->join('layanan_prioritas as lp', 'lp.id', '=', 'transaksi.layanan_prioritas_id')
            ->where('transaksi.cabang_id', $cabangId)
            ->where('transaksi.status', '!=', 'Selesai')
            ->where('transaksi.status', '!=', 'Batal')
            ->where('pegawai_id', $pegawaiId)
            ->orderBy('lp.prioritas', 'desc')
            ->orderBy('transaksi.waktu', 'asc')
            ->select('transaksi.*')
            ->get();
    }

    public function getMonitoringByCabang(int $cabangId): Collection
    {
        return Transaksi::query()
            ->with('pelanggan')
            ->join('detail_transaksi as dt', 'transaksi.id', '=', 'dt.transaksi_id')
            ->join('detail_layanan_transaksi as dlt', 'dt.id', '=', 'dlt.detail_transaksi_id')
            ->join('harga_jenis_layanan as hjl', 'hjl.id', '=', 'dlt.harga_jenis_layanan_id')
            ->join('jenis_layanan as jl', 'jl.id', '=', 'hjl.jenis_layanan_id')
            ->join('jenis_pakaian as jp', 'jp.id', '=', 'hjl.jenis_pakaian_id')
            ->join('detail_gamis as dg', 'dg.id', '=', 'transaksi.gamis_id')
            ->select(
                'transaksi.id as transaksi_id',
                'transaksi.pelanggan_id',
                'transaksi.total_bayar_akhir',
                'dg.nama as nama_gamis',
                DB::raw('DATE(transaksi.waktu) as tanggal'),
                DB::raw('SUM(dt.total_pakaian * hjl.harga) as upah_gamis'),
                'transaksi.total_biaya_layanan_tambahan',
                'transaksi.konfirmasi_upah_gamis'
            )
            ->where('transaksi.cabang_id', $cabangId)
            ->where('jl.for_gamis', true)
            ->where('transaksi.status', 'Selesai')
            ->groupBy(
                'transaksi.id',
                'transaksi.pelanggan_id',
                'transaksi.total_bayar_akhir',
                'dg.nama',
                DB::raw('DATE(transaksi.waktu)'),
                'transaksi.total_biaya_layanan_tambahan',
                'transaksi.konfirmasi_upah_gamis'
            )
            ->orderBy('transaksi.waktu', 'asc')
            ->orderBy('transaksi.gamis_id', 'asc')
            ->get();
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
        return User::query()
            ->join('detail_gamis as dg', 'users.id', '=', 'dg.user_id')
            ->where('users.cabang_id', $cabangId)
            ->get();
    }

    public function getJenisPakaianOptionsByCabang(int $cabangId): Collection
    {
        return JenisPakaian::where('cabang_id', $cabangId)->get();
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
        return Transaksi::where('cabang_id', $cabangId)->where('id', $transaksiId)->update(['status' => $status]);
    }

    public function updateKonfirmasiUpah(string $transaksiId, bool $konfirmasi): void
    {
        Transaksi::where('id', $transaksiId)->update([
            'konfirmasi_upah_gamis' => $konfirmasi,
        ]);
    }

    public function getTransaksiGamisByUser(int $cabangId, int $userId, ?string $tanggal = null): Collection
    {
        $query = Transaksi::query()
            ->with(['pegawai' => function ($query) {
                $query->withTrashed();
            }])
            ->with(['pelanggan:id,nama', 'layananPrioritas:id,nama', 'gamis:id,nama'])
            ->join('detail_gamis as dg', 'dg.id', '=', 'transaksi.gamis_id')
            ->where('dg.user_id', $userId)
            ->where('transaksi.cabang_id', $cabangId)
            ->select('transaksi.*');

        if ($tanggal !== null) {
            $query->where(DB::raw('DATE(transaksi.waktu)'), $tanggal);
        }

        return $query->orderBy('waktu', 'asc')->get();
    }

    public function getMonitoringGamisByUser(int $cabangId, int $userId, ?string $tanggal = null): Collection
    {
        $query = Transaksi::query()
            ->with('pelanggan')
            ->join('detail_transaksi as dt', 'transaksi.id', '=', 'dt.transaksi_id')
            ->join('detail_layanan_transaksi as dlt', 'dt.id', '=', 'dlt.detail_transaksi_id')
            ->join('harga_jenis_layanan as hjl', 'hjl.id', '=', 'dlt.harga_jenis_layanan_id')
            ->join('jenis_layanan as jl', 'jl.id', '=', 'hjl.jenis_layanan_id')
            ->join('jenis_pakaian as jp', 'jp.id', '=', 'hjl.jenis_pakaian_id')
            ->join('detail_gamis as dg', 'dg.id', '=', 'transaksi.gamis_id')
            ->select(
                'transaksi.id as transaksi_id',
                'transaksi.pelanggan_id',
                'transaksi.total_bayar_akhir',
                'dg.nama as nama_gamis',
                DB::raw('DATE(transaksi.waktu) as tanggal'),
                DB::raw('SUM(dt.total_pakaian * hjl.harga) as upah_gamis'),
                'transaksi.total_biaya_layanan_tambahan',
                'transaksi.konfirmasi_upah_gamis'
            )
            ->where('transaksi.cabang_id', $cabangId)
            ->where('jl.for_gamis', true)
            ->where('transaksi.status', 'Selesai')
            ->where('dg.user_id', $userId);

        if ($tanggal !== null) {
            $query->where(DB::raw('DATE(transaksi.waktu)'), $tanggal);
        }

        return $query
            ->groupBy(
                'transaksi.id',
                'transaksi.pelanggan_id',
                'transaksi.total_bayar_akhir',
                'dg.nama',
                DB::raw('DATE(transaksi.waktu)'),
                'transaksi.total_biaya_layanan_tambahan',
                'transaksi.konfirmasi_upah_gamis'
            )
            ->orderBy('transaksi.waktu', 'asc')
            ->orderBy('transaksi.gamis_id', 'asc')
            ->get();
    }

    public function findTransaksiDetailForGamis(int $userId, string $transaksiId): ?Transaksi
    {
        return Transaksi::query()
            ->with(['pegawai' => function ($query) {
                $query->withTrashed();
            }])
            ->with(['pelanggan:id,nama', 'layananPrioritas:id,nama', 'gamis:id,nama'])
            ->join('detail_gamis as dg', 'dg.id', '=', 'transaksi.gamis_id')
            ->where('dg.user_id', $userId)
            ->where('transaksi.id', $transaksiId)
            ->select('transaksi.*')
            ->first();
    }
}
