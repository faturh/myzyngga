<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeuanganToko extends Model
{
    use HasFactory;

    protected $table = 'keuangan_toko';

    protected $fillable = [
        'tanggal',
        'tipe',
        'kategori',
        'nominal',
        'keterangan',
        'cabang_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'double',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }
}
