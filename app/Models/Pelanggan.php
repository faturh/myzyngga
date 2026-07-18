<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Pelanggan extends Model
{
    use HasFactory, HasSlug;

    protected $table = 'pelanggan';
    protected $primaryKey = 'id';
    public $incrementing = "true";
    public $timestamps = "true";
    protected $fillable = [
        'user_id',
        'nama',
        'jenis_kelamin',
        'telepon',
        'email',
        'alamat',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('nama')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function preference()
    {
        return $this->hasOne(CustomerPreference::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }
}
