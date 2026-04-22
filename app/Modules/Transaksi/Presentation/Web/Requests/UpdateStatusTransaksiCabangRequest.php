<?php

namespace App\Modules\Transaksi\Presentation\Web\Requests;

use App\Enums\StatusTransaksi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusTransaksiCabangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'string'],
            'status' => ['required', 'string', Rule::in(array_column(StatusTransaksi::cases(), 'value'))],
            'isJadwal' => ['nullable', 'boolean'],
        ];
    }
}
