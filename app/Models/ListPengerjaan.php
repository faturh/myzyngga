<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListPengerjaan extends Model
{
    use HasFactory;

    protected $table = 'list_pengerjaan';

    protected $fillable = [
        'list_status_pengerjaan_id',
        'list_history_pengerjaan_id',
    ];

    public function statusPengerjaan()
    {
        return $this->belongsTo(ListStatusPengerjaan::class, 'list_status_pengerjaan_id');
    }

    public function historyPengerjaan()
    {
        return $this->belongsTo(ListHistoryPengerjaan::class, 'list_history_pengerjaan_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'list_pengerjaan_id');
    }
}
