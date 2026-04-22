<?php

namespace App\Modules\Order\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'pelanggan_id' => ['required', 'integer', 'exists:pelanggan,id'],
            'cabang_id' => ['required', 'integer', 'exists:cabang,id'],
            'layanan_prioritas_id' => ['required', 'integer', 'exists:layanan_prioritas,id'],
            'pickup_address' => ['required', 'string', 'max:1000'],
            'pickup_detail_address' => ['nullable', 'string', 'max:255'],
            'pickup_date' => ['required', 'date'],
            'pickup_time' => ['required', 'string', 'max:20'],
            'parfum' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:2000'],
            'payment_method' => ['required', 'string', 'in:cash,qris,transfer'],
            'estimated_total' => ['required', 'numeric', 'min:0'],
        ];
    }
}
