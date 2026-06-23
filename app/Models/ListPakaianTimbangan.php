<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListPakaianTimbangan extends Model
{
    use HasFactory;

    protected $table = 'list_pakaian_timbangan';

    protected $fillable = [
        'timbangan_id',
        'jenis_pakaian_id',
        'qty',
    ];

    public function timbangan()
    {
        return $this->belongsTo(Timbangan::class, 'timbangan_id');
    }

    public function jenisPakaian()
    {
        return $this->belongsTo(JenisPakaian::class, 'jenis_pakaian_id');
    }
}
