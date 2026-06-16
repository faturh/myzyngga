<?php

namespace App\Modules\Admin\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransaksiManualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'pelanggan_id' => ['required', 'integer', 'exists:pelanggan,id'],
            'pegawai_id' => ['required', 'integer', 'exists:users,id'],
            'cabang_id' => ['required', 'integer', 'exists:cabang,id'],
            'layanan_prioritas_id' => ['required', 'integer', 'exists:layanan_prioritas,id'],
            'jenis_pembayaran' => ['required', 'string', 'in:cash,qris,transfer'],
            'payment_status' => ['nullable', 'string', 'in:pending,paid,failed'],
            'status' => ['nullable', 'string', 'in:created,picked_up,in_progress,ready_for_delivery,completed,cancelled'],
            'total_biaya_layanan' => ['required', 'numeric', 'min:0'],
            'total_biaya_prioritas' => ['nullable', 'numeric', 'min:0'],
            'total_biaya_layanan_tambahan' => ['nullable', 'numeric', 'min:0'],
            'total_bayar_akhir' => ['required', 'numeric', 'min:0'],
            'bayar' => ['nullable', 'numeric', 'min:0'],
            'kembalian' => ['nullable', 'numeric', 'min:0'],
            'pickup_address' => ['nullable', 'string'],
            'pickup_detail_address' => ['nullable', 'string'],
            'pickup_date' => ['nullable', 'date'],
            'pickup_time' => ['nullable', 'string', 'max:20'],
            'parfum' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
