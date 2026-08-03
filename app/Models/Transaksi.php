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

            // Sync list_status_pengerjaan_id when payment_status changes to paid or status is updated
            $newStatusId = $transaksi->pending_status_id ?? $transaksi->list_status_pengerjaan_id;

            if ($transaksi->isDirty('status') && $transaksi->status) {
                $transaksi->pending_status_id = null;
                $statusMap = [
                    'baru' => 1, 'created' => 1, 'perlu diproses' => 1, 'perlu_diproses' => 1, 'pending' => 1,
                    'menunggu pembayaran' => 2, 'menunggu_pembayaran' => 2,
                    'proses' => 3, 'perlu dikerjakan' => 3, 'perlu_dikerjakan' => 3,
                    'proses pengerjaan' => 4, 'proses_pengerjaan' => 4, 'siap ambil' => 4, 'siap_ambil' => 4, 'in_progress' => 4,
                    'selesai' => 5, 'pesanan selesai' => 5, 'completed' => 5,
                    'kendala' => 6, 'kendala pesanan' => 6, 'kendala_pesanan' => 6,
                    'batal' => 7, 'dibatalkan' => 7, 'cancelled' => 7, 'sedang dibatalkan' => 7, 'sedang_dibatalkan' => 7,
                    'jemput' => 8, 'penjemputan' => 8, 'picked_up' => 8, 'sedang dijemput' => 8, 'sedang_dijemput' => 8, 'menunggu di jemput' => 8, 'menunggu_di_jemput' => 8,
                    'antar' => 9, 'pengantaran' => 9, 'ready_for_delivery' => 9, 'perlu di antar' => 9, 'perlu_di_antar' => 9,
                ];
                $normalized = strtolower(trim((string) $transaksi->status));
                if (isset($statusMap[$normalized])) {
                    $newStatusId = $statusMap[$normalized];
                }
            }

            if (!$newStatusId) {
                $newStatusId = 1;
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
            $wasStatusChanged = ($transaksi->status_changed_to !== null 
                || $transaksi->wasChanged('status') 
                || $transaksi->wasChanged('list_pengerjaan_id'));

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
                $email = $transaksi->pelanggan?->user?->email 
                    ?? $transaksi->pelanggan?->email 
                    ?? ($transaksi->pelanggan?->user_id ? \App\Models\User::find($transaksi->pelanggan->user_id)?->email : null);
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
            $isFinishedStatus = in_array(strtolower(trim((string) $transaksi->status)), ['selesai', 'pesanan selesai'], true)
                || (int) ($transaksi->list_pengerjaan?->list_status_pengerjaan_id) === 5;

            if ($wasStatusChanged && $isFinishedStatus) {
                $pelangganObj = $transaksi->pelanggan;
                $userObj = $pelangganObj?->user;
                $email = $userObj?->email 
                    ?? $pelangganObj?->email 
                    ?? ($pelangganObj?->user_id ? \App\Models\User::find($pelangganObj->user_id)?->email : null);

                if ($email) {
                    \Illuminate\Support\Facades\Mail::to($email)
                        ->send(new \App\Mail\OrderFinishedMail($transaksi));
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
                $meta = json_decode($this->payment_metadata ?? '{}', true) ?? [];
                if ($this->is_roundtrip || isset($meta['pending_delivery']) || $this->list_status_pengerjaan_id == 9) {
                    $statusId = 9;
                } else {
                    $statusId = 5;
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
        $finishedOrDeliveryStatuses = ['perlu di antar', 'perlu_di_antar', 'ready_for_delivery', 'sedang diantar', 'selesai', 'pesanan selesai', 'completed'];

        if ($statusId == 5 || $statusId == 9 || in_array(strtolower((string) $this->status), $finishedOrDeliveryStatuses, true)) {
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
        $baseDate = null;
        if (! empty($this->pickup_date)) {
            $dateStr = \Carbon\Carbon::parse($this->pickup_date)->toDateString();
            $rawTime = ! empty($this->pickup_time) ? trim(explode('-', (string) $this->pickup_time)[0]) : ($this->waktu ? \Carbon\Carbon::parse($this->waktu)->format('H:i:s') : '08:00:00');
            if (strlen($rawTime) === 5) {
                $rawTime .= ':00';
            }
            try {
                $baseDate = \Carbon\Carbon::parse($dateStr . ' ' . $rawTime);
            } catch (\Exception $e) {
                $baseDate = $this->waktu ? \Carbon\Carbon::parse($this->waktu) : now();
            }
        } else {
            $baseDate = $this->waktu ? \Carbon\Carbon::parse($this->waktu) : now();
        }

        if (! $baseDate) {
            $baseDate = now();
        }

        if ($baseDate->hour < 8) {
            $baseDate->setTime(8, 0, 0);
        } elseif ($baseDate->hour >= 20) {
            $baseDate->addDay()->setTime(8, 0, 0);
        }

        $priority = (int) ($this->layananPrioritas->prioritas ?? 1);
        $date = $baseDate->copy();

        if ($priority >= 99) {
            $hoursToAdd = 5;
            while ($hoursToAdd > 0) {
                $endOfDay = $date->copy()->setTime(20, 0, 0);
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

        $daysToAdd = match (true) {
            $priority >= 3 => 1, // Express: 1 Hari (24 Jam)
            $priority >= 2 => 2, // Quick: 2 Hari (48 Jam)
            default => 3,        // Reguler: 3 Hari (72 Jam)
        };

        $etaDate = $date->addDays($daysToAdd);

        if ($etaDate->hour < 8) {
            $etaDate->setTime(8, 0, 0);
        } elseif ($etaDate->hour >= 20) {
            $etaDate->addDay()->setTime(8, 0, 0);
        }

        return $etaDate;
    }
}
