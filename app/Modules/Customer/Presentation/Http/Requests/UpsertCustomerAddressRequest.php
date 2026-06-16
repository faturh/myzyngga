<?php

namespace App\Modules\Customer\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertCustomerAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:1000'],
            'detail_address' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'jenis_kelamin' => ['nullable', 'string', 'in:L,P'],
            'telepon' => ['nullable', 'string', 'max:25'],
        ];
    }
}
