<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiRead extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_reads';

    protected $fillable = [
        'notifikasi_id',
        'pelanggan_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function notifikasi()
    {
        return $this->belongsTo(Notifikasi::class, 'notifikasi_id');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }
}
