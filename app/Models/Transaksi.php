<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'transaksi';
    protected $primaryKey = 'id';
    public $incrementing = "true";
    public $timestamps = "true";
    protected $fillable = [
        'nota',
        'waktu',
        'pickup_address',
        'pickup_detail_address',
        'pickup_date',
        'pickup_time',
        'parfum',
        'catatan',
        'is_roundtrip',
        'total_biaya_layanan',
        'total_biaya_prioritas',
        'total_biaya_layanan_tambahan',
        'total_bayar_akhir',
        'jenis_pembayaran',
        'payment_status',
        'paid_at',
        'bayar',
        'kembalian',
        'status',
        'bukti_timbangan',
        'layanan_prioritas_id',
        'pelanggan_id',
        'pegawai_id',
        'cabang_id',
    ];

    protected $casts = [
        'waktu' => 'datetime',
        'pickup_date' => 'date',
        'paid_at' => 'datetime',
        'is_roundtrip' => 'boolean',
    ];

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function layananTambahanTransaksi()
    {
        return $this->hasMany(LayananTambahanTransaksi::class);
    }

    public function layananPrioritas()
    {
        return $this->belongsTo(LayananPrioritas::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    protected static function booted()
    {
        static::saving(function ($transaksi) {
            if (isset($transaksi->attributes['pegawai_id']) && isset($transaksi->cabang_id)) {
                $rawPegawaiId = $transaksi->getRawPegawaiId();
                if ($rawPegawaiId !== null) {
                    $transaksi->attributes['pegawai_id'] = $transaksi->cabang_id . '_' . $rawPegawaiId;
                }
            }
        });
    }

    public function getRawPegawaiId()
    {
        $val = $this->attributes['pegawai_id'] ?? null;
        if (!$val) return null;
        if (strpos($val, '_') !== false) {
            $parts = explode('_', $val);
            return (int) end($parts);
        }
        return (int) $val;
    }

    public function getUserIdAttribute()
    {
        return $this->getRawPegawaiId();
    }

    public function pegawai()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
