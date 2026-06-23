<?php

namespace App\Exports;

use App\Models\JenisPakaian;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JenisPakaianExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct()
    {
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return JenisPakaian::orderBy('nama', 'asc')->get();
    }

    public function map($data): array
    {
        return [
            $data->nama,
        ];
    }

    public function headings(): array
    {
        return [
            'nama_pakaian',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
