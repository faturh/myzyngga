<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListStatusPengerjaan extends Model
{
    use HasFactory;

    protected $table = 'list_status_pengerjaan';

    protected $fillable = [
        'nama',
    ];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'list_status_pengerjaan_id');
    }
}
