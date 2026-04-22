<?php

namespace App\Modules\Transaksi\Presentation\Web\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteTransaksiCabangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'transaksi_id' => ['required', 'string'],
        ];
    }
}
