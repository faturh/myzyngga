<?php

namespace App\Modules\Order\Presentation\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nota' => $this->nota,
            'status' => match($this->status) {
                'Perlu Diproses', 'Baru', 'created' => 'created',
                'Sedang Dijemput', 'Jemput', 'picked_up' => 'picked_up',
                'Proses Pengerjaan', 'Proses', 'Perlu Dikerjakan', 'Menunggu Pembayaran', 'in_progress' => 'in_progress',
                'ready_for_delivery' => 'ready_for_delivery',
                'Pesanan Selesai', 'Selesai', 'completed' => 'completed',
                'Sedang Dibatalkan', 'Batal', 'cancelled' => 'cancelled',
                default => $this->status,
            },
            'pickup' => [
                'address' => $this->pickup_address,
                'detail_address' => $this->pickup_detail_address,
                'date' => optional($this->pickup_date)->format('Y-m-d'),
                'time' => $this->pickup_time,
            ],
            'payment' => [
                'method' => $this->jenis_pembayaran,
                'status' => $this->payment_status,
                'total' => $this->total_bayar_akhir,
            ],
            'customer' => [
                'id' => $this->pelanggan_id,
                'name' => optional($this->pelanggan)->nama,
            ],
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),

            // Raw fields for skripsi
            'waktu' => $this->waktu,
            'pickup_address' => $this->pickup_address,
            'pickup_detail_address' => $this->pickup_detail_address,
            'pickup_date' => $this->pickup_date,
            'pickup_time' => $this->pickup_time,
            'parfum' => $this->parfum,
            'catatan' => $this->catatan,
            'total_biaya_layanan' => $this->total_biaya_layanan,
            'total_biaya_prioritas' => $this->total_biaya_prioritas,
            'total_biaya_layanan_tambahan' => $this->total_biaya_layanan_tambahan,
            'total_bayar_akhir' => $this->total_bayar_akhir,
            'jenis_pembayaran' => $this->jenis_pembayaran,
            'payment_status' => $this->payment_status,
            'paid_at' => $this->paid_at,
            'bayar' => $this->bayar,
            'kembalian' => $this->kembalian,
            'layanan_prioritas_id' => $this->layanan_prioritas_id,
            'pelanggan_id' => $this->pelanggan_id,
            'pegawai_id' => $this->pegawai_id,
            'cabang_id' => $this->cabang_id,
            'list_pengerjaan_id' => $this->list_pengerjaan_id,

            // Relations
            'layanan_prioritas' => $this->layananPrioritas,
            'timbangan' => $this->timbangan,
            'pegawai' => $this->pegawai,
            'pelanggan' => $this->pelanggan,
            'list_pengerjaan' => $this->listPengerjaan,
        ];
    }
}
