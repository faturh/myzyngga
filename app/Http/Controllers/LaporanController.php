<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function laporanPendapatanLaundry(Request $request)
    {
        $title = "Laporan Pendapatan Laundry";
        $cabang = Cabang::withTrashed()->get();
        $nama_cabang = null;

        $tanggalAwal = $request->tanggalAwal ? $request->tanggalAwal : Carbon::now()->format('Y-') . Carbon::now()->format('m-') . '01';
        $tanggalAkhir = $request->tanggalAkhir ? $request->tanggalAkhir : Carbon::now()->format('Y-m-d');

        $transaksi = Transaksi::query()
            ->with(['pegawai' => function($query) {
                $query->withTrashed();
            }])
            ->with(['pelanggan:id,nama', 'layananPrioritas'])
            ->join('cabang as c', 'c.id', '=', 'transaksi.cabang_id')
            ->join('list_pengerjaan as lpen', 'lpen.id', '=', 'transaksi.list_pengerjaan_id')
            ->select('transaksi.nota', DB::raw("DATE(transaksi.waktu) as tanggal"), 'transaksi.layanan_prioritas_id', 'transaksi.total_bayar_akhir', 'transaksi.pelanggan_id', 'transaksi.pegawai_id', 'transaksi.total_bayar_akhir as pendapatan_laundry', 'c.nama as nama_cabang', 'c.id as cabang_id')
            ->where(DB::raw("DATE(transaksi.waktu)"), '>=', $tanggalAwal)
            ->where(DB::raw("DATE(transaksi.waktu)"), '<=', $tanggalAkhir)
            ->where('lpen.list_status_pengerjaan_id', 5)
            ->groupBy('transaksi.nota', DB::raw("DATE(transaksi.waktu)"), 'transaksi.layanan_prioritas_id', 'transaksi.total_bayar_akhir', 'transaksi.pelanggan_id', 'transaksi.pegawai_id', 'c.nama', 'c.id')
            ->orderBy('transaksi.waktu', 'asc')
            ->get();

        $transaksiTidakGamis = collect();

        if ($request->cabang_id) {
            $transaksi = $transaksi->where('cabang_id', $request->cabang_id);
            $nama_cabang = $cabang->where('id', $request->cabang_id)->first();
        }

        if (auth()->user()->roles[0]->name == 'manajer_laundry') {
            $cabangId = Cabang::withTrashed()->where('id', auth()->user()->cabang_id)->first();
            $transaksi = $transaksi->where('cabang_id', $cabangId->id);
            $nama_cabang = $cabangId;
        }

        if (auth()->user()->roles[0]->name == 'manajer_laundry' && $request->cabang_id) {
            abort(403, 'USER DOES NOT HAVE THE RIGHT ROLES.');
        }

        return view('operator.admin.laporan.pendapatan-laundry', compact('title', 'transaksi', 'cabang', 'nama_cabang', 'transaksiTidakGamis'));
    }

    public function pdfLaporanPendapatanLaundry(Request $request)
    {
        $title = "Laporan Pendapatan Laundry";
        $nama_cabang = null;

        $tanggalAwal = $request->tanggalAwal ? $request->tanggalAwal : Carbon::now()->format('Y-') . Carbon::now()->format('m-') . '01';
        $tanggalAkhir = $request->tanggalAkhir ? $request->tanggalAkhir : Carbon::now()->format('Y-m-d');

        $transaksi = Transaksi::query()
            ->with(['pegawai' => function($query) {
                $query->withTrashed();
            }])
            ->with(['pelanggan:id,nama', 'layananPrioritas'])
            ->join('cabang as c', 'c.id', '=', 'transaksi.cabang_id')
            ->join('list_pengerjaan as lpen', 'lpen.id', '=', 'transaksi.list_pengerjaan_id')
            ->select('transaksi.nota', DB::raw("DATE(transaksi.waktu) as tanggal"), 'transaksi.layanan_prioritas_id', 'transaksi.total_bayar_akhir', 'transaksi.pelanggan_id', 'transaksi.pegawai_id', 'transaksi.total_bayar_akhir as pendapatan_laundry', 'c.nama as nama_cabang', 'c.id as cabang_id')
            ->where(DB::raw("DATE(transaksi.waktu)"), '>=', $tanggalAwal)
            ->where(DB::raw("DATE(transaksi.waktu)"), '<=', $tanggalAkhir)
            ->where('lpen.list_status_pengerjaan_id', 5)
            ->groupBy('transaksi.nota', DB::raw("DATE(transaksi.waktu)"), 'transaksi.layanan_prioritas_id', 'transaksi.total_bayar_akhir', 'transaksi.pelanggan_id', 'transaksi.pegawai_id', 'c.nama', 'c.id')
            ->orderBy('transaksi.waktu', 'asc')
            ->get();

        $transaksiTidakGamis = collect();

        if ($request->cabang_id) {
            $transaksi = $transaksi->where('cabang_id', $request->cabang_id);
            $nama_cabang = Cabang::withTrashed()->where('id', $request->cabang_id)->first();
        }

        if (auth()->user()->roles[0]->name == 'manajer_laundry') {
            $cabangId = Cabang::withTrashed()->where('id', auth()->user()->cabang_id)->first();
            $transaksi = $transaksi->where('cabang_id', $cabangId->id);
            $nama_cabang = $cabangId;
        }

        if (auth()->user()->roles[0]->name == 'manajer_laundry' && $request->cabang_id) {
            abort(403, 'USER DOES NOT HAVE THE RIGHT ROLES.');
        }

        $pdf = Pdf::loadView('operator.admin.laporan.pdf.pendapatan-laundry', [
            'judul' => $title,
            'judulTabel' => $title,
            'transaksi' => $transaksi,
            'transaksiTidakGamis' => $transaksiTidakGamis,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'nama_cabang' => $nama_cabang,
            'footer' => $title
        ])
        ->setPaper('a4', 'landscape');
        return $pdf->stream();
    }

    public function laporanPelanggan(Request $request)
    {
        $title = "Laporan Pelanggan";
        $cabang = Cabang::withTrashed()->get();
        $nama_cabang = null;

        $tanggalAwal = $request->tanggalAwal ? $request->tanggalAwal : Carbon::now()->format('Y-') . Carbon::now()->format('m');
        $tanggalAkhir = $request->tanggalAkhir ? $request->tanggalAkhir : Carbon::now()->format('Y-m');

        $driver = DB::connection()->getDriverName();
        if ($driver === 'pgsql') {
            $monthFunc = "CAST(EXTRACT(MONTH FROM transaksi.waktu) AS INTEGER)";
            $yearFunc = "CAST(EXTRACT(YEAR FROM transaksi.waktu) AS INTEGER)";
        } elseif ($driver === 'sqlite') {
            $monthFunc = "CAST(strftime('%m', transaksi.waktu) AS INTEGER)";
            $yearFunc = "CAST(strftime('%Y', transaksi.waktu) AS INTEGER)";
        } else {
            $monthFunc = "MONTH(transaksi.waktu)";
            $yearFunc = "YEAR(transaksi.waktu)";
        }

        $transaksi = Transaksi::query()
            ->with(['cabang' => function($query) {
                $query->withTrashed();
            }])
            ->join('cabang as c', 'c.id', '=', 'transaksi.cabang_id')
            ->join('pelanggan as p', 'p.id', '=', 'transaksi.pelanggan_id')
            ->join('list_pengerjaan as lpen', 'lpen.id', '=', 'transaksi.list_pengerjaan_id')
            ->select('transaksi.pelanggan_id', 'p.nama as nama_pelanggan', DB::raw("COUNT(transaksi.id) as total_transaksi"), DB::raw("SUM(transaksi.total_bayar_akhir) as total_pengeluaran"), DB::raw("$monthFunc as bulan"), DB::raw("$yearFunc as tahun"), 'c.id as cabang_id', 'c.nama as nama_cabang')
            ->where(function ($query) use ($tanggalAwal, $tanggalAkhir, $monthFunc, $yearFunc) {
                $query->where(function ($subQuery) use ($tanggalAwal, $monthFunc, $yearFunc) {
                    $subQuery->where(DB::raw($yearFunc), '>', Carbon::parse($tanggalAwal)->format('Y'))
                        ->orWhere(function ($nestedQuery) use ($tanggalAwal, $monthFunc, $yearFunc) {
                            $nestedQuery->where(DB::raw($yearFunc), '=', Carbon::parse($tanggalAwal)->format('Y'))
                                ->where(DB::raw($monthFunc), '>=', Carbon::parse($tanggalAwal)->format('m'));
                        });
                })->where(function ($subQuery) use ($tanggalAkhir, $monthFunc, $yearFunc) {
                    $subQuery->where(DB::raw($yearFunc), '<', Carbon::parse($tanggalAkhir)->format('Y'))
                        ->orWhere(function ($nestedQuery) use ($tanggalAkhir, $monthFunc, $yearFunc) {
                            $nestedQuery->where(DB::raw($yearFunc), '=', Carbon::parse($tanggalAkhir)->format('Y'))
                                ->where(DB::raw($monthFunc), '<=', Carbon::parse($tanggalAkhir)->format('m'));
                        });
                });
            })
            ->where('lpen.list_status_pengerjaan_id', 5)
            ->groupBy('transaksi.pelanggan_id', 'p.nama', DB::raw($monthFunc), DB::raw($yearFunc), 'c.id', 'c.nama')
            ->orderBy('transaksi.waktu', 'asc')
            ->get();

        if ($request->cabang_id) {
            $transaksi = $transaksi->where('cabang_id', $request->cabang_id);
            $nama_cabang = $cabang->where('id', $request->cabang_id)->first();
        }

        if (auth()->user()->roles[0]->name == 'manajer_laundry') {
            $cabangId = Cabang::withTrashed()->where('id', auth()->user()->cabang_id)->first();
            $transaksi = $transaksi->where('cabang_id', $cabangId->id);
            $nama_cabang = $cabangId;
        }

        if (auth()->user()->roles[0]->name == 'manajer_laundry' && $request->cabang_id) {
            abort(403, 'USER DOES NOT HAVE THE RIGHT ROLES.');
        }

        return view('operator.admin.laporan.pelanggan', compact('title', 'transaksi', 'cabang', 'nama_cabang'));
    }

    public function pdfLaporanPelanggan(Request $request)
    {
        $title = "Laporan Pelanggan";
        $cabang = Cabang::withTrashed()->get();
        $nama_cabang = null;

        $tanggalAwal = $request->tanggalAwal ? $request->tanggalAwal : Carbon::now()->format('Y-') . Carbon::now()->format('m');
        $tanggalAkhir = $request->tanggalAkhir ? $request->tanggalAkhir : Carbon::now()->format('Y-m');

        $driver = DB::connection()->getDriverName();
        if ($driver === 'pgsql') {
            $monthFunc = "CAST(EXTRACT(MONTH FROM transaksi.waktu) AS INTEGER)";
            $yearFunc = "CAST(EXTRACT(YEAR FROM transaksi.waktu) AS INTEGER)";
        } elseif ($driver === 'sqlite') {
            $monthFunc = "CAST(strftime('%m', transaksi.waktu) AS INTEGER)";
            $yearFunc = "CAST(strftime('%Y', transaksi.waktu) AS INTEGER)";
        } else {
            $monthFunc = "MONTH(transaksi.waktu)";
            $yearFunc = "YEAR(transaksi.waktu)";
        }

        $transaksi = Transaksi::query()
            ->with(['cabang' => function($query) {
                $query->withTrashed();
            }])
            ->join('cabang as c', 'c.id', '=', 'transaksi.cabang_id')
            ->join('pelanggan as p', 'p.id', '=', 'transaksi.pelanggan_id')
            ->join('list_pengerjaan as lpen', 'lpen.id', '=', 'transaksi.list_pengerjaan_id')
            ->select('transaksi.pelanggan_id', 'p.nama as nama_pelanggan', DB::raw("COUNT(transaksi.id) as total_transaksi"), DB::raw("SUM(transaksi.total_bayar_akhir) as total_pengeluaran"), DB::raw("$monthFunc as bulan"), DB::raw("$yearFunc as tahun"), 'c.id as cabang_id', 'c.nama as nama_cabang')
            ->where(function ($query) use ($tanggalAwal, $tanggalAkhir, $monthFunc, $yearFunc) {
                $query->where(function ($subQuery) use ($tanggalAwal, $monthFunc, $yearFunc) {
                    $subQuery->where(DB::raw($yearFunc), '>', Carbon::parse($tanggalAwal)->format('Y'))
                        ->orWhere(function ($nestedQuery) use ($tanggalAwal, $monthFunc, $yearFunc) {
                            $nestedQuery->where(DB::raw($yearFunc), '=', Carbon::parse($tanggalAwal)->format('Y'))
                                ->where(DB::raw($monthFunc), '>=', Carbon::parse($tanggalAwal)->format('m'));
                        });
                })->where(function ($subQuery) use ($tanggalAkhir, $monthFunc, $yearFunc) {
                    $subQuery->where(DB::raw($yearFunc), '<', Carbon::parse($tanggalAkhir)->format('Y'))
                        ->orWhere(function ($nestedQuery) use ($tanggalAkhir, $monthFunc, $yearFunc) {
                            $nestedQuery->where(DB::raw($yearFunc), '=', Carbon::parse($tanggalAkhir)->format('Y'))
                                ->where(DB::raw($monthFunc), '<=', Carbon::parse($tanggalAkhir)->format('m'));
                        });
                });
            })
            ->where('lpen.list_status_pengerjaan_id', 5)
            ->groupBy('transaksi.pelanggan_id', 'p.nama', DB::raw($monthFunc), DB::raw($yearFunc), 'c.id', 'c.nama')
            ->orderBy('transaksi.waktu', 'asc')
            ->get();

        if ($request->cabang_id) {
            $transaksi = $transaksi->where('cabang_id', $request->cabang_id);
            $nama_cabang = $cabang->where('id', $request->cabang_id)->first();
        }

        if (auth()->user()->roles[0]->name == 'manajer_laundry') {
            $cabangId = Cabang::withTrashed()->where('id', auth()->user()->cabang_id)->first();
            $transaksi = $transaksi->where('cabang_id', $cabangId->id);
            $nama_cabang = $cabangId;
        }

        if (auth()->user()->roles[0]->name == 'manajer_laundry' && $request->cabang_id) {
            abort(403, 'USER DOES NOT HAVE THE RIGHT ROLES.');
        }

        $pdf = Pdf::loadView('operator.admin.laporan.pdf.pelanggan', [
            'judul' => $title,
            'judulTabel' => $title,
            'transaksi' => $transaksi,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'nama_cabang' => $nama_cabang,
            'footer' => $title
        ])
        ->setPaper('a4', 'landscape');
        return $pdf->stream();
    }
}

