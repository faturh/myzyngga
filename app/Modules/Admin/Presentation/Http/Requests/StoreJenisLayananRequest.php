<?php

namespace App\Modules\Admin\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJenisLayananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'for_gamis' => ['required', 'boolean'],
            'cabang_id' => ['required', 'integer', 'exists:cabang,id'],
        ];
    }
}
