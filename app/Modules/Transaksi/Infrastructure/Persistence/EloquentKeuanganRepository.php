<?php

namespace App\Modules\Transaksi\Infrastructure\Persistence;

use App\Models\KeuanganToko;
use App\Models\Transaksi;
use App\Modules\Transaksi\Domain\Repositories\KeuanganRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentKeuanganRepository implements KeuanganRepositoryInterface
{
    /**
     * Get all financial records (manual and paid transaction income) based on filters.
     */
    public function allByCabang(?int $cabangId, ?string $filterType, ?string $startDate, ?string $endDate): Collection
    {
        $cabangId = null; // Unify all branches, ignore cabang filter.
        
        // 1. Fetch manual inputs (all manual pemasukan and pengeluaran)
        $manualQuery = KeuanganToko::with('cabang');
        if ($cabangId !== null) {
            $manualQuery->where('cabang_id', $cabangId);
        }
        if ($startDate && $endDate) {
            $manualQuery->whereBetween('tanggal', [$startDate, $endDate]);
        }
        $manualRecords = $manualQuery->get()->map(function($record) {
            return [
                'id' => $record->id,
                'source' => 'manual',
                'tanggal' => $record->tanggal->toDateString(),
                'tipe' => $record->tipe,
                'kategori' => $record->kategori,
                'nominal' => (double) $record->nominal,
                'keterangan' => $record->keterangan,
                'nama_cabang' => $record->cabang?->nama ?? 'Pusat/Semua',
            ];
        });

        // 2. Fetch transaction income (paid transactions)
        $txQuery = Transaksi::with(['cabang', 'pelanggan'])
            ->where('payment_status', 'paid');
        if ($cabangId !== null) {
            $txQuery->where('cabang_id', $cabangId);
        }
        if ($startDate && $endDate) {
            $txQuery->whereBetween(DB::raw('DATE(waktu)'), [$startDate, $endDate]);
        }
        $txRecords = $txQuery->get()->map(function($tx) {
            return [
                'id' => $tx->id,
                'source' => 'transaksi',
                'tanggal' => \Carbon\Carbon::parse($tx->waktu)->toDateString(),
                'tipe' => 'pemasukan',
                'kategori' => 'Pendapatan Laundry',
                'nominal' => (double) $tx->total_bayar_akhir,
                'keterangan' => 'Pesanan ' . $tx->nota . ' (' . ($tx->pelanggan?->nama ?? 'Guest') . ')',
                'nama_cabang' => $tx->cabang?->nama ?? '-',
            ];
        });

        // 3. Merge and sort by date descending
        return $manualRecords->concat($txRecords)->sortByDesc('tanggal')->values();
    }

    /**
     * Save a manual financial record.
     */
    public function save(array $data): KeuanganToko
    {
        return KeuanganToko::create($data);
    }

    /**
     * Delete a manual financial record.
     */
    public function delete(int $id): bool
    {
        $record = KeuanganToko::find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }

    /**
     * Calculate dynamic store balance.
     */
    public function getStoreBalance(?int $cabangId): float
    {
        $cabangId = null; // Unify all branches, ignore cabang filter.

        // Sum of all paid transactions
        $queryTx = Transaksi::query()->where('payment_status', 'paid');
        if ($cabangId !== null) {
            $queryTx->where('cabang_id', $cabangId);
        }
        $transactionsSum = (double) $queryTx->sum('total_bayar_akhir');

        // Sum of manual pemasukan
        $queryManualIn = KeuanganToko::query()->where('tipe', 'pemasukan');
        if ($cabangId !== null) {
            $queryManualIn->where('cabang_id', $cabangId);
        }
        $manualInSum = (double) $queryManualIn->sum('nominal');

        // Sum of manual pengeluaran
        $queryManualOut = KeuanganToko::query()->where('tipe', 'pengeluaran');
        if ($cabangId !== null) {
            $queryManualOut->where('cabang_id', $cabangId);
        }
        $manualOutSum = (double) $queryManualOut->sum('nominal');

        return $transactionsSum + $manualInSum - $manualOutSum;
    }
}
