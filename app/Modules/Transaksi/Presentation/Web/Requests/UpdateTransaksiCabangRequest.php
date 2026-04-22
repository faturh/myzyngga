<?php

namespace App\Modules\Transaksi\Presentation\Web\Requests;

use App\Enums\JenisPembayaran;
use App\Enums\StatusTransaksi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransaksiCabangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'pelanggan_id' => ['required', 'integer', 'exists:pelanggan,id'],
            'gamis_id' => ['nullable', 'integer', 'exists:detail_gamis,id'],
            'total_biaya_layanan' => ['required', 'numeric', 'min:0'],
            'total_biaya_prioritas' => ['required', 'numeric', 'min:0'],
            'total_biaya_layanan_tambahan' => ['required', 'numeric', 'min:0'],
            'total_bayar_akhir' => ['required', 'numeric', 'min:0'],
            'jenis_pembayaran' => ['required', 'string', Rule::in(array_column(JenisPembayaran::cases(), 'value'))],
            'bayar' => ['required', 'numeric', 'min:0'],
            'kembalian' => ['required', 'numeric'],
            'status' => ['required', 'string', Rule::in(array_column(StatusTransaksi::cases(), 'value'))],
            'layanan_prioritas_id' => ['required', 'integer', 'exists:layanan_prioritas,id'],
            'layanan_tambahan_id' => ['nullable', 'array'],
            'layanan_tambahan_id.*' => ['integer', 'exists:layanan_tambahan,id'],
            'detail_transaksi_id' => ['nullable', 'array'],
            'detail_transaksi_id.*' => ['integer', 'exists:detail_transaksi,id'],
            'jenis_pakaian_id' => ['required', 'array', 'min:1'],
            'jenis_pakaian_id.*' => ['required', 'integer', 'exists:jenis_pakaian,id'],
            'jenis_layanan_id' => ['required', 'array', 'min:1'],
            'jenis_layanan_id.*' => ['required', 'array', 'min:1'],
            'jenis_layanan_id.*.*' => ['required', 'integer', 'exists:jenis_layanan,id'],
            'harga_jenis_layanan_id' => ['required', 'array', 'min:1'],
            'harga_jenis_layanan_id.*' => ['required', 'numeric', 'min:0'],
            'total_pakaian' => ['required', 'array', 'min:1'],
            'total_pakaian.*' => ['required', 'integer', 'min:1'],
        ];
    }
}
