<?php

namespace App\Http\Controllers;

use App\Modules\Transaksi\Application\DTO\UpsertTransaksiData;
use App\Modules\Transaksi\Application\Services\TransaksiDashboardService;
use App\Modules\Transaksi\Presentation\Web\Requests\DeleteTransaksiCabangRequest;
use App\Modules\Transaksi\Presentation\Web\Requests\StoreTransaksiCabangRequest;
use App\Modules\Transaksi\Presentation\Web\Requests\UpdateStatusTransaksiCabangRequest;
use App\Modules\Transaksi\Presentation\Web\Requests\UpdateTransaksiCabangRequest;
use App\Shared\Exceptions\DomainException;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function __construct(
        private readonly TransaksiDashboardService $transaksiDashboardService,
    ) {}

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

    public function storeTransaksiCabang(StoreTransaksiCabangRequest $request)
    {
        try {
            $transaksi = $this->transaksiDashboardService->storeTransaksiCabang(
                auth()->user(),
                UpsertTransaksiData::fromArray($request->validated()),
            );

            return response()->json([
                'message' => 'Transaksi berhasil ditambahkan.',
                'data' => [
                    'id' => $transaksi->id,
                ],
            ]);
        } catch (DomainException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->status());
        }
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

    public function updateTransaksiCabang(UpdateTransaksiCabangRequest $request)
    {
        try {
            $transaksiId = (string) ($request->route('transaksi') ?? $request->input('transaksi_id'));
            if ($transaksiId === '') {
                return response()->json([
                    'message' => 'Parameter transaksi tidak ditemukan.',
                ], 422);
            }

            $updated = $this->transaksiDashboardService->updateTransaksiCabang(
                auth()->user(),
                $transaksiId,
                UpsertTransaksiData::fromArray($request->validated()),
            );

            return response()->json([
                'message' => 'Transaksi berhasil diperbarui.',
                'data' => [
                    'updated' => $updated,
                ],
            ]);
        } catch (DomainException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->status());
        }
    }

    public function editStatusTransaksiCabang(Request $request)
    {
        return $this->transaksiDashboardService->editStatusTransaksiCabang(auth()->user(), $request);
    }

    public function updateStatusTransaksiCabang(UpdateStatusTransaksiCabangRequest $request)
    {
        $updated = $this->transaksiDashboardService->updateStatusTransaksiCabang(
            auth()->user(),
            (string) $request->validated('id'),
            (string) $request->validated('status'),
        );

        $cabangSlug = $request->route('cabang');

        if ($cabangSlug) {
            if ($request->boolean('isJadwal')) {
                return $updated
                    ? to_route('transaksi.lurah.cabang.jadwal', ['cabang' => $cabangSlug])->with('success', 'Status Transaksi Berhasil Diperbarui')
                    : to_route('transaksi.lurah.cabang.jadwal', ['cabang' => $cabangSlug])->with('error', 'Status Transaksi Gagal Diperbarui');
            }

            return $updated
                ? to_route('transaksi.lurah.cabang', ['cabang' => $cabangSlug])->with('success', 'Status Transaksi Berhasil Diperbarui')
                : to_route('transaksi.lurah.cabang', ['cabang' => $cabangSlug])->with('error', 'Status Transaksi Gagal Diperbarui');
        }

        if ($request->boolean('isJadwal')) {
            return $updated
                ? to_route('transaksi.jadwal')->with('success', 'Status Transaksi Berhasil Diperbarui')
                : to_route('transaksi.jadwal')->with('error', 'Status Transaksi Gagal Diperbarui');
        }

        return $updated
            ? to_route('transaksi')->with('success', 'Status Transaksi Berhasil Diperbarui')
            : to_route('transaksi')->with('error', 'Status Transaksi Gagal Diperbarui');
    }

    public function deleteTransaksiCabang(DeleteTransaksiCabangRequest $request)
    {
        try {
            $this->transaksiDashboardService->deleteTransaksiCabang(auth()->user(), (string) $request->validated('transaksi_id'));

            return response()->json([
                'message' => 'Transaksi berhasil dihapus.',
            ]);
        } catch (DomainException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->status());
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

        return view('operator.dashboard.transaksi.struk.index', $payload);
    }

}


