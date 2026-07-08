<?php

namespace App\Modules\Customer\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'label'          => ['nullable', 'string', 'max:100'],
            'address'        => ['sometimes', 'required', 'string', 'max:1000'],
            'detail_address' => ['nullable', 'string', 'max:255'],
            'lat'            => ['nullable', 'numeric'],
            'lng'            => ['nullable', 'numeric'],
        ];
    }
}
