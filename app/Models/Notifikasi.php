<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    public const JENIS_STATUS = 'status';
    public const JENIS_KURIR_JEMPUT = 'kurir_jemput';
    public const JENIS_KURIR_ANTAR = 'kurir_antar';
    public const JENIS_SELESAI = 'selesai';
    public const JENIS_JAM_OPERASIONAL = 'jam_operasional';

    protected $fillable = [
        'pelanggan_id',
        'transaksi_id',
        'jenis',
        'pesan',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}
