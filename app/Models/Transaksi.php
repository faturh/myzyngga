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
        'pickup_lat',
        'pickup_lng',
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
        'midtrans_order_id',
        'payment_metadata',
    ];

    protected $casts = [
        'waktu' => 'datetime',
        'pickup_date' => 'date',
        'paid_at' => 'datetime',
        'is_roundtrip' => 'boolean',
    ];

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

        static::updated(function ($transaksi) {
            // 1. Kirim email jika pembayaran di-update menjadi paid (Lunas)
            if ($transaksi->wasChanged('payment_status') && $transaksi->payment_status === 'paid') {
                $email = $transaksi->pelanggan->user->email ?? null;
                if ($email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($email)
                            ->send(new \App\Mail\PaymentConfirmedMail($transaksi));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Mail Error (Payment Confirmed): ' . $e->getMessage());
                    }
                }
            }

            // 2. Kirim email jika status laundry di-update menjadi 'Selesai'
            if ($transaksi->wasChanged('status') && $transaksi->status === 'Selesai') {
                $email = $transaksi->pelanggan->user->email ?? null;
                if ($email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($email)
                            ->send(new \App\Mail\OrderFinishedMail($transaksi));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Mail Error (Order Finished): ' . $e->getMessage());
                    }
                }
            }
        });
    }

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

    public function notaKeluar()
    {
        return $this->hasMany(NotaKeluar::class, 'transaksi_id');
    }

    public function upgradeLayanans()
    {
        return $this->hasMany(UpgradeLayanan::class, 'transaksi_id')->orderBy('created_at', 'desc');
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

    public function timbangan()
    {
        return $this->hasOne(Timbangan::class, 'transaksi_id');
    }
}
