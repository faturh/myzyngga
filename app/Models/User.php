<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'slug',
        'email',
        'password',
        'cabang_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getNameAttribute(): string
    {
        return (string) ($this->attributes['username'] ?? '');
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || ($this->attributes['role'] ?? null) === 'admin';
    }

    /**
     * Check if the user is a customer.
     */
    public function isCustomer(): bool
    {
        return $this->hasRole('customer') || ($this->attributes['role'] ?? null) === 'customer';
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function lurah()
    {
        return $this->hasMany(Lurah::class);
    }

    public function pic()
    {
        return $this->hasMany(PIC::class);
    }

    public function manajer()
    {
        return $this->hasMany(ManajerLaundry::class);
    }

    public function pegawai()
    {
        return $this->hasMany(PegawaiLaundry::class);
    }

    public function rw()
    {
        return $this->hasMany(RW::class);
    }

    public function gamis()
    {
        return $this->hasMany(DetailGamis::class);
    }
}
