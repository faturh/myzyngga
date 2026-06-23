<?php

namespace App\Imports;

use App\Models\Cabang;
use App\Models\JenisPakaian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JenisPakaianImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $nama = JenisPakaian::where('nama', $row['nama_pakaian'])->first();
        if (empty($nama)) {
            return new JenisPakaian([
                'nama' => $row['nama_pakaian'],
            ]);
        }
    }
}
