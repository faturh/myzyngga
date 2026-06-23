<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPakaian extends Model
{
    use HasFactory;

    protected $table = 'jenis_pakaian';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'nama',
    ];

    public function hargaJenisLayanan()
    {
        return $this->hasMany(HargaJenisLayanan::class);
    }
}
