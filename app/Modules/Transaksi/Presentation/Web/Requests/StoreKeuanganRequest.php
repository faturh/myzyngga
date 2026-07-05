<?php

namespace App\Modules\Transaksi\Presentation\Web\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKeuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isAdmin() || $this->user()->hasRole('manajer_laundry'));
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'tipe' => ['required', 'string', Rule::in(['pemasukan', 'pengeluaran'])],
            'kategori' => ['required', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'min:0.01'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'cabang_id' => ['nullable', 'integer', 'exists:cabang,id'],
        ];
    }
}
