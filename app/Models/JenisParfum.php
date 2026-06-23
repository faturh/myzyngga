<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisParfum extends Model
{
    use HasFactory;

    protected $table = 'jenis_parfum';

    public $timestamps = false;

    protected $fillable = [
        'nama',
    ];
}
