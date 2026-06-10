<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProsesTransaksiItem extends Model
{
    use HasFactory;

    protected $table = 'proses_transaksi_items';

    protected $fillable = [
        'proses_transaksi_id',
        'nama_item',
        'qty',
    ];

    public function prosesTransaksi()
    {
        return $this->belongsTo(ProsesTransaksi::class, 'proses_transaksi_id');
    }
}
