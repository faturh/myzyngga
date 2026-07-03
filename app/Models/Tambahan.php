<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tambahan extends Model
{
    use HasFactory;

    protected $table = 'tambahan';

    protected $fillable = [
        'tambahan_id',
        'kategori_pakaian_satuan_id',
        'jumlah',
        'harga_akhir',
    ];

    public function kategoriPakaianSatuan()
    {
        return $this->belongsTo(KategoriPakaianSatuan::class, 'kategori_pakaian_satuan_id');
    }
}
