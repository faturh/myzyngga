<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $title = "Dashboard";
        $userRole = auth()->user()->roles[0]->name;

        if ($userRole == 'lurah' || $userRole == 'pic') {
            $cabang = null;
            $jmlCabang = Cabang::count();
            $jmlUser = User::count();

            $transaksiBaru = Transaksi::where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))->where('list_status_pengerjaan_id', 1)->count();
            $transaksiProses = Transaksi::where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))->where('list_status_pengerjaan_id', 3)->count();
            $transaksiSiapDiambil = Transaksi::where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))->where('list_status_pengerjaan_id', 4)->count();
            $transaksiPengantaran = 0;
            $transaksiPenjemputan = Transaksi::where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))->where('list_status_pengerjaan_id', 8)->count();
            $transaksiSelesai = Transaksi::where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))->where('list_status_pengerjaan_id', 5)->count();
            $transaksiBatal = Transaksi::where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))->where('list_status_pengerjaan_id', 7)->count();

            $jadwalLayanan = Transaksi::query()
                ->join('layanan_prioritas as lp', 'lp.id', '=', 'transaksi.layanan_prioritas_id')
                ->join('cabang as c', 'c.id', '=', 'transaksi.cabang_id')
                ->where('transaksi.list_status_pengerjaan_id', '!=', 5)
                ->where('transaksi.list_status_pengerjaan_id', '!=', 7)
                ->where(DB::raw('DATE(transaksi.waktu)'), Carbon::now()->format('Y-m-d'))
                ->orderBy('lp.prioritas', 'desc')
                ->orderBy('transaksi.waktu', 'asc')
                ->select('transaksi.*', 'c.slug as cabang_slug', 'c.nama as cabang_nama')
                ->get();

            $pendapatanHari = Transaksi::query()
                ->where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))
                ->where('list_status_pengerjaan_id', 5)
                ->sum('total_bayar_akhir');

            $transaksiBulanan = Transaksi::query()
                ->where(DB::raw('YEAR(waktu)'), Carbon::now()->format('Y'))
                ->where('list_status_pengerjaan_id', 5)
                ->groupBy(DB::raw('MONTH(waktu)'))
                ->select(DB::raw('MONTH(waktu) as bulan'), DB::raw('SUM(total_bayar_akhir) as hasil'))
                ->get()
                ->keyBy('bulan');
            $pendapatanBulanan = [];
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $pendapatanBulanan[$bulan] = [
                    'bulan' => $bulan,
                    'hasil' => isset($transaksiBulanan[$bulan]) ? $transaksiBulanan[$bulan]->hasil : 0,
                ];
            }

            $transaksiTahunan = Transaksi::query()
                ->where('list_status_pengerjaan_id', 5)
                ->groupBy(DB::raw('YEAR(waktu)'))
                ->select(DB::raw('YEAR(waktu) as tahun'), DB::raw('SUM(total_bayar_akhir) as hasil'))
                ->get()
                ->keyBy('tahun');
            $pendapatanTahunan = [];
            foreach ($transaksiTahunan as $item => $value) {
                $pendapatanTahunan[$item] = [
                    'tahun' => $item,
                    'hasil' => isset($transaksiTahunan[$item]) ? $transaksiTahunan[$item]->hasil : 0,
                ];
            }

            return view('operator.dashboard.index', compact('title', 'userRole', 'cabang', 'jmlCabang', 'jmlUser', 'transaksiBaru', 'transaksiProses', 'transaksiSiapDiambil', 'transaksiPengantaran', 'transaksiPenjemputan', 'transaksiSelesai', 'transaksiBatal', 'jadwalLayanan', 'pendapatanHari', 'pendapatanBulanan', 'pendapatanTahunan'));

        } else if ($userRole == 'manajer_laundry' || $userRole == 'pegawai_laundry') {
            $cabang = Cabang::where('id', auth()->user()->cabang_id)->first();
            $jmlUser = User::where('cabang_id', $cabang->id)->count();
            $jmlCabang = null;

            $transaksiBaru = Transaksi::where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))->where('cabang_id', $cabang->id)->where('list_status_pengerjaan_id', 1)->count();
            $transaksiProses = Transaksi::where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))->where('cabang_id', $cabang->id)->where('list_status_pengerjaan_id', 3)->count();
            $transaksiSiapDiambil = Transaksi::where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))->where('cabang_id', $cabang->id)->where('list_status_pengerjaan_id', 4)->count();
            $transaksiPengantaran = 0;
            $transaksiPenjemputan = Transaksi::where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))->where('cabang_id', $cabang->id)->where('list_status_pengerjaan_id', 8)->count();
            $transaksiSelesai = Transaksi::where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))->where('cabang_id', $cabang->id)->where('list_status_pengerjaan_id', 5)->count();
            $transaksiBatal = Transaksi::where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))->where('cabang_id', $cabang->id)->where('list_status_pengerjaan_id', 7)->count();

            $jadwalLayanan = Transaksi::query()
                ->join('layanan_prioritas as lp', 'lp.id', '=', 'transaksi.layanan_prioritas_id')
                ->join('cabang as c', 'c.id', '=', 'transaksi.cabang_id')
                ->where('transaksi.cabang_id', $cabang->id)
                ->where('transaksi.list_status_pengerjaan_id', '!=', 5)
                ->where('transaksi.list_status_pengerjaan_id', '!=', 7)
                ->where(DB::raw('DATE(transaksi.waktu)'), Carbon::now()->format('Y-m-d'))
                ->orderBy('lp.prioritas', 'desc')
                ->orderBy('transaksi.waktu', 'asc')
                ->select('transaksi.*', 'c.nama as cabang_nama')
                ->get();

            $pendapatanHari = Transaksi::query()
                ->where('cabang_id', $cabang->id)
                ->where(DB::raw('DATE(waktu)'), Carbon::now()->format('Y-m-d'))
                ->where('list_status_pengerjaan_id', 5)
                ->sum('total_bayar_akhir');

            $transaksiBulanan = Transaksi::query()
                ->where('cabang_id', $cabang->id)
                ->where(DB::raw('YEAR(waktu)'), Carbon::now()->format('Y'))
                ->where('list_status_pengerjaan_id', 5)
                ->groupBy(DB::raw('MONTH(waktu)'))
                ->select(DB::raw('MONTH(waktu) as bulan'), DB::raw('SUM(total_bayar_akhir) as hasil'))
                ->get()
                ->keyBy('bulan');
            $pendapatanBulanan = [];
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $pendapatanBulanan[$bulan] = [
                    'bulan' => $bulan,
                    'hasil' => isset($transaksiBulanan[$bulan]) ? $transaksiBulanan[$bulan]->hasil : 0,
                ];
            }

            $transaksiTahunan = Transaksi::query()
                ->where('cabang_id', $cabang->id)
                ->where('list_status_pengerjaan_id', 5)
                ->groupBy(DB::raw('YEAR(waktu)'))
                ->select(DB::raw('YEAR(waktu) as tahun'), DB::raw('SUM(total_bayar_akhir) as hasil'))
                ->get()
                ->keyBy('tahun');
            $pendapatanTahunan = [];
            foreach ($transaksiTahunan as $item => $value) {
                $pendapatanTahunan[$item] = [
                    'tahun' => $item,
                    'hasil' => isset($transaksiTahunan[$item]) ? $transaksiTahunan[$item]->hasil : 0,
                ];
            }

            return view('operator.dashboard.index', compact('title', 'userRole', 'cabang', 'jmlCabang', 'jmlUser', 'transaksiBaru', 'transaksiProses', 'transaksiSiapDiambil', 'transaksiPengantaran', 'transaksiPenjemputan', 'transaksiSelesai', 'transaksiBatal', 'jadwalLayanan', 'pendapatanHari', 'pendapatanBulanan', 'pendapatanTahunan'));
        }

        abort(403, 'Unauthorized role.');
    }
}

