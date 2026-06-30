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
        'list_status_pengerjaan_id',
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

            // Sync list_status_pengerjaan_id when payment_status changes to paid
            if (isset($transaksi->attributes['payment_status']) && $transaksi->attributes['payment_status'] === 'paid') {
                $currentStatusId = $transaksi->attributes['list_status_pengerjaan_id'] ?? null;
                if ($currentStatusId == 2 || $currentStatusId === null) {
                    $transaksi->attributes['list_status_pengerjaan_id'] = 3;
                    $transaksi->attributes['status'] = 'Proses';
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

    public function statusPengerjaan()
    {
        return $this->belongsTo(ListStatusPengerjaan::class, 'list_status_pengerjaan_id');
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value;
        
        $mapping = [
            'baru' => 1,
            'created' => 1,
            'proses' => ($this->payment_status === 'paid' ? 3 : 2),
            'siap ambil' => 4,
            'siap_ambil' => 4,
            'antar' => 4,
            'pengantaran' => 4,
            'selesai' => 5,
            'completed' => 5,
            'kendala' => 6,
            'batal' => 7,
            'dibatalkan' => 7,
            'cancelled' => 7,
            'jemput' => 8,
            'penjemputan' => 8,
            'picked_up' => 8,
        ];
        
        $normalized = strtolower(trim($value ?? ''));
        if (isset($mapping[$normalized])) {
            $this->attributes['list_status_pengerjaan_id'] = $mapping[$normalized];
        }
    }

    public function setListStatusPengerjaanIdAttribute($value)
    {
        $this->attributes['list_status_pengerjaan_id'] = $value;
        
        $mapping = [
            1 => 'Baru',
            2 => 'Proses',
            3 => 'Proses',
            4 => 'Siap Ambil',
            5 => 'Selesai',
            6 => 'Kendala',
            7 => 'Batal',
            8 => 'Jemput',
        ];
        
        if (isset($mapping[$value])) {
            $this->attributes['status'] = $mapping[$value];
        }
    }
}
