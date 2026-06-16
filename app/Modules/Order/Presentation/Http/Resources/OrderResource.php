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
            'status' => $this->status,
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
                'name' => optional($this->whenLoaded('pelanggan'))->nama,
            ],
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
