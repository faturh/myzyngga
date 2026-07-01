<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListHistoryPengerjaan extends Model
{
    use HasFactory;

    protected $table = 'list_history_pengerjaan';

    protected $fillable = [
        'transaksi_id',
        'status_sebelumnya',
        'status_sesudahnya',
        'operator_id',
        'keterangan',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function statusSebelumnya()
    {
        return $this->belongsTo(ListStatusPengerjaan::class, 'status_sebelumnya');
    }

    public function statusSesudahnya()
    {
        return $this->belongsTo(ListStatusPengerjaan::class, 'status_sesudahnya');
    }
}
