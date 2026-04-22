<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Cabang;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\JenisLayanan;
use App\Models\JenisPakaian;
use Illuminate\Http\Request;
use App\Enums\JenisPembayaran;
use App\Enums\StatusTransaksi;
use App\Models\DetailTransaksi;
use App\Models\LayananTambahan;
use Illuminate\Validation\Rule;
use App\Models\LayananPrioritas;
use App\Models\HargaJenisLayanan;
use Illuminate\Support\Facades\DB;
use App\Models\DetailLayananTransaksi;
use App\Models\LayananTambahanTransaksi;
use App\Modules\Transaksi\Application\Services\TransaksiDashboardService;
use App\Shared\Exceptions\DomainException;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    public function __construct(
        private readonly TransaksiDashboardService $transaksiDashboardService,
    ) {
    }

    public function index()
    {
        try {
            $payload = $this->transaksiDashboardService->indexData(auth()->user());
            return view($payload['view'], $payload['data']);
        } catch (DomainException $exception) {
            abort($exception->status(), $exception->getMessage());
        }
    }

    public function indexJadwal()
    {
        try {
            $payload = $this->transaksiDashboardService->jadwalData(auth()->user());
            return view($payload['view'], $payload['data']);
        } catch (DomainException $exception) {
            if ($exception->status() === 404) {
                return to_route('transaksi');
            }
            abort($exception->status(), $exception->getMessage());
        }
    }

    public function indexCabang(Request $request)
    {
        try {
            $payload = $this->transaksiDashboardService->cabangData(auth()->user(), (string) $request->cabang);
            return view($payload['view'], $payload['data']);
        } catch (DomainException $exception) {
            abort($exception->status(), $exception->getMessage());
        }
    }

    public function indexCabangJadwal(Request $request)
    {
        try {
            $payload = $this->transaksiDashboardService->cabangJadwalData(auth()->user(), (string) $request->cabang);
            return view($payload['view'], $payload['data']);
        } catch (DomainException $exception) {
            abort($exception->status(), $exception->getMessage());
        }
    }

    public function viewDetailTransaksi(Request $request)
    {
        try {
            $payload = $this->transaksiDashboardService->viewDetailTransaksiData(auth()->user(), $request);
            return view($payload['view'], $payload['data']);
        } catch (DomainException $exception) {
            abort($exception->status(), $exception->getMessage());
        }
    }

    public function createTransaksiCabang(Request $request)
    {
        try {
            $payload = $this->transaksiDashboardService->createTransaksiCabangData(auth()->user(), $request);
            return view($payload['view'], $payload['data']);
        } catch (DomainException $exception) {
            abort($exception->status(), $exception->getMessage());
        }
    }

    public function storeTransaksiCabang(Request $request)
    {
        return $this->transaksiDashboardService->storeTransaksiCabang(auth()->user(), $request);
    }

    public function editTransaksiCabang(Request $request)
    {
        try {
            $payload = $this->transaksiDashboardService->editTransaksiCabangData(auth()->user(), $request);
            return view($payload['view'], $payload['data']);
        } catch (DomainException $exception) {
            abort($exception->status(), $exception->getMessage());
        }
    }

    public function updateTransaksiCabang(Request $request)
    {
        return $this->transaksiDashboardService->updateTransaksiCabang(auth()->user(), $request);
    }

    public function editStatusTransaksiCabang(Request $request)
    {
        return $this->transaksiDashboardService->editStatusTransaksiCabang(auth()->user(), $request);
    }

    public function updateStatusTransaksiCabang(Request $request)
    {
        return $this->transaksiDashboardService->updateStatusTransaksiCabang(auth()->user(), $request);
    }

    public function deleteTransaksiCabang(Request $request)
    {
        try {
            $this->transaksiDashboardService->deleteTransaksiCabang(auth()->user(), $request);
            abort(200, 'Transaksi Berhasil Dihapus');
        } catch (DomainException $exception) {
            abort($exception->status(), $exception->getMessage());
        }
    }

    public function ubahJenisPakaian(Request $request)
    {
        return $this->transaksiDashboardService->ubahJenisPakaian(auth()->user(), $request);
    }

    public function ubahJenisLayanan(Request $request)
    {
        return $this->transaksiDashboardService->ubahJenisLayanan(auth()->user(), $request);
    }

    public function ubahLayananTambahan(Request $request)
    {
        return $this->transaksiDashboardService->ubahLayananTambahan(auth()->user(), $request);
    }

    public function hitungTotalBayar(Request $request)
    {
        return $this->transaksiDashboardService->hitungTotalBayar(auth()->user(), $request);
    }

    public function cetakStrukTransaksi(Request $request)
    {
        $payload = $this->transaksiDashboardService->cetakStrukTransaksiData($request);
        return view('dashboard.transaksi.struk.index', $payload);
    }

    public function konfirmasiUpah(Request $request)
    {
        $this->transaksiDashboardService->konfirmasiUpah($request);
    }

    public function transaksiGamisHarian()
    {
        try {
            $payload = $this->transaksiDashboardService->transaksiGamisData(auth()->user(), true);
            return view('dashboard.transaksi.gamis.index', $payload);
        } catch (DomainException $exception) {
            abort($exception->status(), $exception->getMessage());
        }
    }

    public function transaksiGamisSemua()
    {
        try {
            $payload = $this->transaksiDashboardService->transaksiGamisData(auth()->user(), false);
            return view('dashboard.transaksi.gamis.index', $payload);
        } catch (DomainException $exception) {
            abort($exception->status(), $exception->getMessage());
        }
    }

    public function viewDetailTransaksiGamis(Request $request)
    {
        $payload = $this->transaksiDashboardService->viewDetailTransaksiGamisData(auth()->user(), $request);
        return view('dashboard.transaksi.gamis.lihat', $payload);
    }
}
