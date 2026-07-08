<?php

namespace App\Modules\Transaksi\Presentation\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Modules\Transaksi\Application\Services\KeuanganService;
use App\Modules\Transaksi\Presentation\Web\Requests\StoreKeuanganRequest;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function __construct(
        private readonly KeuanganService $keuanganService
    ) {}

    /**
     * Display the financial records list with filters.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $cabangId = $request->query('cabang_id');

        // Manager is restricted to their own branch
        if ($user->hasRole('manajer_laundry')) {
            $cabangId = $user->cabang_id;
        }

        $filterType = $request->query('filter_type', 'daily');
        $dateValue = $request->query('date_value');

        // Resolve filtered records
        $filtered = $this->keuanganService->getFilteredRecords(
            $cabangId ? (int)$cabangId : null,
            $filterType,
            $dateValue
        );

        // Resolve overall store balance (not filtered by date range, but filtered by branch if applicable)
        $saldoToko = $this->keuanganService->getStoreBalance($cabangId ? (int)$cabangId : null);

        $cabangs = Cabang::all();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'records' => $filtered['records'],
                    'totalPemasukan' => $filtered['totalPemasukan'],
                    'totalPengeluaran' => $filtered['totalPengeluaran'],
                    'saldoToko' => $saldoToko,
                    'startDate' => $filtered['startDate'],
                    'endDate' => $filtered['endDate'],
                    'dateValue' => $filtered['dateValue'],
                    'filterType' => $filtered['filterType'],
                ],
                'status' => 200
            ], 200);
        }

        return view('operator.admin.keuangan', [
            'records' => $filtered['records'],
            'totalPemasukan' => $filtered['totalPemasukan'],
            'totalPengeluaran' => $filtered['totalPengeluaran'],
            'startDate' => $filtered['startDate'],
            'endDate' => $filtered['endDate'],
            'dateValue' => $filtered['dateValue'],
            'filterType' => $filtered['filterType'],
            'saldoToko' => $saldoToko,
            'cabangId' => $cabangId,
            'cabangs' => $cabangs,
        ]);
    }

    /**
     * Store a new manual financial record.
     */
    public function store(StoreKeuanganRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();

        // Enforce manager's branch restriction
        if ($user->hasRole('manajer_laundry')) {
            $validated['cabang_id'] = $user->cabang_id;
        }

        $record = $this->keuanganService->addRecord($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $record,
                'message' => 'Catatan keuangan berhasil disimpan.',
                'status' => 200
            ], 200);
        }

        return redirect()->back()->with('success', 'Catatan keuangan berhasil disimpan.');
    }

    /**
     * Delete a manual financial record.
     */
    public function destroy(int $id)
    {
        $deleted = $this->keuanganService->deleteRecord($id);

        if ($deleted) {
            if (request()->expectsJson()) {
                return response()->json([
                    'data' => ['id' => $id],
                    'message' => 'Catatan keuangan berhasil dihapus.',
                    'status' => 200
                ], 200);
            }
            return redirect()->back()->with('success', 'Catatan keuangan berhasil dihapus.');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Catatan keuangan tidak ditemukan atau tidak dapat dihapus.',
                'status' => 400
            ], 400);
        }

        return redirect()->back()->withErrors(['error' => 'Catatan keuangan tidak ditemukan atau tidak dapat dihapus.']);
    }
}
