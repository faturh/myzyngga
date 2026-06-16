<?php

namespace App\Modules\Admin\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCabangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255', 'unique:cabang,nama'],
            'lokasi' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
