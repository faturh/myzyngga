<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProsesTransaksi extends Model
{
    use HasFactory;

    protected $table = 'proses_transaksi';

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
        return $this->hasMany(ProsesTransaksiItem::class, 'proses_transaksi_id');
    }
}
