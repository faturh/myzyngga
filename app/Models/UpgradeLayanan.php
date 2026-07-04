<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UpgradeLayanan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'transaksi_id',
        'layanan_asal_id',
        'layanan_tujuan_id',
        'biaya_upgrade',
        'metode_bayar',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function layananAsal()
    {
        return $this->belongsTo(LayananPrioritas::class, 'layanan_asal_id');
    }

    public function layananTujuan()
    {
        return $this->belongsTo(LayananPrioritas::class, 'layanan_tujuan_id');
    }
}
