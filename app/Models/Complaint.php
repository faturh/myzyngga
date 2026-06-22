<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaints';

    protected $fillable = [
        'transaksi_id',
        'pelanggan_id',
        'content',
        'status',
        'issue_types',
        'image_path',
    ];

    protected $casts = [
        'issue_types' => 'array',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
}
