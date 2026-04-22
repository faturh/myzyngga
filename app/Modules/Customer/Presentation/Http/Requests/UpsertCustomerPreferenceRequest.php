<?php

namespace App\Modules\Customer\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertCustomerPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'default_parfum' => ['nullable', 'string', 'max:100'],
            'default_note' => ['nullable', 'string', 'max:1000'],
            'default_payment_method' => ['nullable', 'string', 'in:cash,qris,transfer'],
            'jenis_kelamin' => ['nullable', 'string', 'in:L,P'],
            'telepon' => ['nullable', 'string', 'max:25'],
        ];
    }
}
