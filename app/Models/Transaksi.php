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
        'gaji_dibayar',
        'cabang_id',
        'midtrans_order_id',
        'payment_metadata',
        'list_pengerjaan_id',
        'fk_tambahan',
    ];

    protected $casts = [
        'waktu' => 'datetime',
        'pickup_date' => 'date',
        'paid_at' => 'datetime',
        'is_roundtrip' => 'boolean',
        'gaji_dibayar' => 'boolean',
    ];

    public $pending_status_id = null;
    public $status_changed_from = null;
    public $status_changed_to = null;

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
            $newStatusId = $transaksi->pending_status_id ?? $transaksi->list_status_pengerjaan_id;
            if (!$newStatusId) {
                $newStatusId = 1;
            }

            if (strtolower(trim($transaksi->payment_status ?? '')) === 'paid') {
                if ($newStatusId == 2) {
                    $newStatusId = $transaksi->is_roundtrip ? 9 : 5;
                }
            }

            $transaksi->attributes['status'] = $transaksi->getStatusName($newStatusId);

            // Generate UUID if not set
            if (!$transaksi->id) {
                $transaksi->id = (string) \Illuminate\Support\Str::uuid();
            }

            $oldStatusId = null;
            $listPengerjaan = null;

            if ($transaksi->list_pengerjaan_id) {
                $listPengerjaan = ListPengerjaan::find($transaksi->list_pengerjaan_id);
                if ($listPengerjaan) {
                    $oldStatusId = $listPengerjaan->list_status_pengerjaan_id;
                }
            }

            if (!$listPengerjaan || $oldStatusId != $newStatusId) {
                // Prepare active list_pengerjaan
                if (!$listPengerjaan) {
                    $listPengerjaan = new ListPengerjaan();
                }
                $listPengerjaan->list_status_pengerjaan_id = $newStatusId;
                $listPengerjaan->save();

                $transaksi->list_pengerjaan_id = $listPengerjaan->id;

                // Store status change details to log in saved/created event
                $transaksi->status_changed_from = $oldStatusId;
                $transaksi->status_changed_to = $newStatusId;
                $transaksi->pending_status_id = null;
            }
        });

        static::saved(function ($transaksi) {
            if ($transaksi->status_changed_to !== null) {
                $oldStatusId = $transaksi->status_changed_from;
                $newStatusId = $transaksi->status_changed_to;

                // Create history log
                $history = new ListHistoryPengerjaan();
                $history->transaksi_id = $transaksi->id;
                $history->status_sebelumnya = $oldStatusId;
                $history->status_sesudahnya = $newStatusId;
                $history->operator_id = auth()->id();
                $history->keterangan = "Status diubah dari " . ($oldStatusId ? ($transaksi->getStatusName($oldStatusId)) : 'N/A') . " ke " . $transaksi->getStatusName($newStatusId);
                $history->save();

                // Link active list_pengerjaan to this history
                if ($transaksi->list_pengerjaan_id) {
                    $listPengerjaan = ListPengerjaan::find($transaksi->list_pengerjaan_id);
                    if ($listPengerjaan) {
                        $listPengerjaan->list_history_pengerjaan_id = $history->id;
                        $listPengerjaan->saveQuietly();
                    }
                }

                // Reset variables
                $transaksi->status_changed_from = null;
                $transaksi->status_changed_to = null;
            }

            // 1. Kirim email jika pembayaran di-update menjadi paid (Lunas).
            // Guest tidak punya akun User, jadi fallback ke email yang mereka
            // isi sendiri saat checkout (disimpan di pelanggan.email).
            if ($transaksi->wasChanged('payment_status') && $transaksi->payment_status === 'paid') {
                $email = $transaksi->pelanggan->user->email ?? $transaksi->pelanggan->email ?? null;
                if ($email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($email)
                            ->send(new \App\Mail\PaymentConfirmedMail($transaksi));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Mail Error (Payment Confirmed): ' . $e->getMessage());
                    }
                }
            }

            // 2. Kirim email jika status laundry di-update menjadi selesai. getStatusName(5)
            // menghasilkan 'Pesanan Selesai', bukan literal 'Selesai' — perbandingan lama
            // tidak pernah cocok sehingga email "pesanan selesai" tidak pernah terkirim.
            $statusChanged = $transaksi->wasChanged('list_pengerjaan_id') || $transaksi->wasChanged('status');
            if ($statusChanged && in_array($transaksi->status, ['Selesai', 'Pesanan Selesai'], true)) {
                $email = $transaksi->pelanggan->user->email ?? $transaksi->pelanggan->email ?? null;
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

    public function tambahanSatuan()
    {
        return $this->hasMany(Tambahan::class, 'tambahan_id', 'fk_tambahan');
    }

    public function listPengerjaan()
    {
        return $this->belongsTo(ListPengerjaan::class, 'list_pengerjaan_id');
    }

    public function statusPengerjaan()
    {
        return $this->belongsTo(ListStatusPengerjaan::class, 'list_pengerjaan_id'); // Just dummy mapping or fallback
    }

    public function getListStatusPengerjaanIdAttribute()
    {
        if ($this->list_pengerjaan_id) {
            if ($this->relationLoaded('listPengerjaan') && $this->listPengerjaan) {
                return $this->listPengerjaan->list_status_pengerjaan_id;
            }
            return $this->listPengerjaan()->value('list_status_pengerjaan_id') ?? $this->pending_status_id ?? null;
        }
        return $this->pending_status_id ?? null;
    }

    public function setListStatusPengerjaanIdAttribute($value)
    {
        $id = (int)$value;
        if ($id === 5) {
            $paymentStatus = strtolower(trim($this->payment_status ?? ''));
            if ($paymentStatus !== 'paid') {
                $id = 2;
            }
        }
        $this->pending_status_id = $id;
        $this->attributes['status'] = $this->getStatusName($id);
    }

    public function getStatusName($id)
    {
        $statusNames = [
            1 => 'Perlu Diproses',
            2 => 'Menunggu Pembayaran',
            3 => 'Perlu Dikerjakan',
            4 => 'Proses Pengerjaan',
            5 => 'Pesanan Selesai',
            6 => 'Kendala Pesanan',
            7 => 'Sedang Dibatalkan',
            8 => 'Menunggu di Jemput',
            9 => 'Perlu di Antar',
        ];
        return $statusNames[$id] ?? 'Perlu Diproses';
    }

    public function setStatusAttribute($value)
    {
        $normalized = strtolower(trim($value ?? ''));
        $statusId = 1; // Default
        
        if (in_array($normalized, ['baru', 'created', 'perlu diproses', 'perlu_diproses'])) {
            $statusId = 1;
        } elseif (in_array($normalized, ['menunggu pembayaran', 'menunggu_pembayaran'])) {
            $statusId = 2;
        } elseif (in_array($normalized, ['proses', 'perlu dikerjakan', 'perlu_dikerjakan'])) {
            $statusId = 3;
        } elseif (in_array($normalized, ['proses pengerjaan', 'proses_pengerjaan', 'siap ambil', 'siap_ambil', 'in_progress'])) {
            $statusId = 4;
        } elseif (in_array($normalized, ['selesai', 'completed', 'pesanan selesai', 'pesanan_selesai'])) {
            $paymentStatus = strtolower(trim($this->payment_status ?? ''));
            if ($paymentStatus === 'paid') {
                if ($this->list_status_pengerjaan_id == 9) {
                    $statusId = 5;
                } else {
                    $statusId = $this->is_roundtrip ? 9 : 5;
                }
            } else {
                $statusId = 2;
            }
        } elseif (in_array($normalized, ['kendala', 'kendala pesanan', 'kendala_pesanan'])) {
            $statusId = 6;
        } elseif (in_array($normalized, ['batal', 'dibatalkan', 'cancelled', 'sedang dibatalkan', 'sedang_dibatalkan'])) {
            $statusId = 7;
        } elseif (in_array($normalized, ['jemput', 'penjemputan', 'picked_up', 'sedang dijemput', 'sedang_dijemput', 'menunggu di jemput', 'menunggu_di_jemput'])) {
            $statusId = 8;
        } elseif (in_array($normalized, ['antar', 'pengantaran', 'ready_for_delivery', 'perlu di antar', 'perlu_di_antar'])) {
            $statusId = 9;
        }

        $this->pending_status_id = $statusId;
        $this->attributes['status'] = $this->getStatusName($statusId);
    }

    public function canBeUpgraded(): bool
    {
        $statusId = $this->listPengerjaan?->list_status_pengerjaan_id;
        // Finished status is 5
        if ($statusId == 5 || strtolower($this->status) === 'selesai' || strtolower($this->status) === 'pesanan selesai') {
            return false;
        }

        $currentPriority = $this->layananPrioritas;
        if (!$currentPriority) {
            return false;
        }

        $availableUpgrades = \App\Models\LayananPrioritas::where('cabang_id', $currentPriority->cabang_id)
            ->where('prioritas', '>', $currentPriority->prioritas)
            ->get();

        if ($availableUpgrades->isEmpty()) {
            return false;
        }

        $baseDate = \Carbon\Carbon::parse($this->waktu ?? now());
        foreach ($availableUpgrades as $upgrade) {
            $maxElapsedHours = match(strtolower($upgrade->nama)) {
                'kilat' => 3,
                'express' => 12,
                'quick' => 24,
                default => 24,
            };
            
            if (now()->lte($baseDate->copy()->addHours($maxElapsedHours))) {
                return true;
            }
        }

        return false;
    }

    public function getEstimasiPengerjaanJam(): int
    {
        $priority = (int) ($this->layananPrioritas->prioritas ?? 1);
        return match (true) {
            $priority >= 99 => 5,  // Kilat
            $priority >= 3 => 10,  // Express
            $priority >= 2 => 20,  // Quick
            default => 30,         // Reguler
        };
    }

    public function getDeadlineWaktu(): \Carbon\Carbon
    {
        $baseDate = $this->pickup_date ?? $this->waktu ?? now();
        $hoursToAdd = $this->getEstimasiPengerjaanJam();

        $date = \Carbon\Carbon::parse($baseDate);

        if ($date->hour < 8) {
            $date->setTime(8, 0, 0);
        } elseif ($date->hour >= 18) {
            $date->addDay()->setTime(8, 0, 0);
        }

        while ($hoursToAdd > 0) {
            $endOfDay = $date->copy()->setTime(18, 0, 0);
            $minutesLeftToday = $date->diffInMinutes($endOfDay, false);
            
            if ($minutesLeftToday <= 0) {
                $date->addDay()->setTime(8, 0, 0);
                continue;
            }

            $minutesToAdd = $hoursToAdd * 60;

            if ($minutesToAdd <= $minutesLeftToday) {
                $date->addMinutes($minutesToAdd);
                $hoursToAdd = 0;
            } else {
                $date->addDay()->setTime(8, 0, 0);
                $hoursToAdd -= ($minutesLeftToday / 60);
            }
        }

        return $date;
    }
}
