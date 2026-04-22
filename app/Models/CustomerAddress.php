<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelanggan_id',
        'label',
        'address',
        'detail_address',
        'lat',
        'lng',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
}
