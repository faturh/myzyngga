<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timbangan extends Model
{
    use HasFactory;

    protected $table = 'timbangan';

    protected $fillable = [
        'transaksi_id',
        'nota',
        'actual_weight',
        'minimum_weight',
        'price_per_kg',
        'charged_weight',
        'total_price',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function items()
    {
        return $this->hasMany(ListPakaianTimbangan::class, 'timbangan_id');
    }
}
