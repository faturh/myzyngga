<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPakaianSatuan extends Model
{
    use HasFactory;

    protected $table = 'kategori_pakaian_satuan';

    protected $fillable = [
        'nama_pakaian',
        'harga',
    ];
}
