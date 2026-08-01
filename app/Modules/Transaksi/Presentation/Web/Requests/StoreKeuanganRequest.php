<?php

namespace App\Modules\Transaksi\Presentation\Web\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKeuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        return $user->isAdmin() ||
            in_array($user->role, ['admin', 'operator', 'kasir', 'manajer_laundry', 'owner']) ||
            $user->hasAnyRole(['admin', 'operator', 'kasir', 'manajer_laundry', 'owner']);
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
