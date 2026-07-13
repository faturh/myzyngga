<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryGaji extends Model
{
    use HasFactory;

    protected $table = 'history_gaji';

    protected $fillable = [
        'pegawai_id',
        'nominal',
        'tanggal',
        'bank',
        'nomor_rekening',
        'keterangan',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'nominal' => 'double',
    ];

    public function pegawai()
    {
        return $this->belongsTo(User::class, 'pegawai_id');
    }
}
