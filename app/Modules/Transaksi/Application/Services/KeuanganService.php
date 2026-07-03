<?php

namespace App\Modules\Transaksi\Application\Services;

use App\Models\KeuanganToko;
use App\Modules\Transaksi\Domain\Repositories\KeuanganRepositoryInterface;
use Carbon\Carbon;

class KeuanganService
{
    public function __construct(
        private readonly KeuanganRepositoryInterface $repository
    ) {}

    /**
     * Get filtered records and cash flow summary.
     *
     * @param int|null $cabangId
     * @param string|null $filterType (daily, weekly, monthly)
     * @param string|null $dateValue
     * @return array
     */
    public function getFilteredRecords(?int $cabangId, ?string $filterType, ?string $dateValue): array
    {
        $startDate = null;
        $endDate = null;

        if (empty($filterType)) {
            $filterType = 'daily';
        }

        if ($filterType === 'daily') {
            $date = !empty($dateValue) ? Carbon::parse($dateValue) : Carbon::now('Asia/Jakarta');
            $startDate = $date->toDateString();
            $endDate = $date->toDateString();
            $dateValue = $startDate;
        } elseif ($filterType === 'weekly') {
            if (!empty($dateValue) && strpos($dateValue, '-W') !== false) {
                $parts = explode('-W', $dateValue);
                $year = (int)$parts[0];
                $week = (int)$parts[1];
                $date = Carbon::now('Asia/Jakarta')->setISODate($year, $week);
            } else {
                $date = !empty($dateValue) ? Carbon::parse($dateValue) : Carbon::now('Asia/Jakarta');
                $dateValue = $date->format('Y-\W') . str_pad($date->weekOfYear, 2, '0', STR_PAD_LEFT);
            }
            $startDate = $date->copy()->startOfWeek()->toDateString();
            $endDate = $date->copy()->endOfWeek()->toDateString();
        } elseif ($filterType === 'monthly') {
            if (!empty($dateValue) && strlen($dateValue) === 7) {
                $date = Carbon::parse($dateValue . '-01');
            } else {
                $date = !empty($dateValue) ? Carbon::parse($dateValue) : Carbon::now('Asia/Jakarta');
                $dateValue = $date->format('Y-m');
            }
            $startDate = $date->copy()->startOfMonth()->toDateString();
            $endDate = $date->copy()->endOfMonth()->toDateString();
        }

        $records = $this->repository->allByCabang($cabangId, $filterType, $startDate, $endDate);
        
        $totalPemasukan = $records->where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = $records->where('tipe', 'pengeluaran')->sum('nominal');

        return [
            'records' => $records,
            'totalPemasukan' => (double) $totalPemasukan,
            'totalPengeluaran' => (double) $totalPengeluaran,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateValue' => $dateValue,
            'filterType' => $filterType,
        ];
    }

    /**
     * Save a manual financial record.
     */
    public function addRecord(array $data): KeuanganToko
    {
        return $this->repository->save($data);
    }

    /**
     * Delete a manual financial record.
     */
    public function deleteRecord(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Calculate dynamic store balance.
     */
    public function getStoreBalance(?int $cabangId): float
    {
        return $this->repository->getStoreBalance($cabangId);
    }
}
