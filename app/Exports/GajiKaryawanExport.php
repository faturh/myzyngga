<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GajiKaryawanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'ID Karyawan',
            'Nama Karyawan',
            'Jabatan / Role',
            'Tarif Gaji per Kg',
            'Total Kg Dikerjakan',
            'Total Gaji Diterima'
        ];
    }

    public function map($row): array
    {
        return [
            $row['id'],
            $row['name'],
            $row['role'],
            'Rp ' . number_format($row['gaji_per_kg'], 0, ',', '.'),
            $row['total_kg'] . ' kg',
            'Rp ' . number_format($row['total_gaji'], 0, ',', '.')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
