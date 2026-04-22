<?php

namespace App\Modules\Customer\Presentation\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $defaultAddress = $this->addresses->firstWhere('is_default', true);

        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'telepon' => $this->telepon,
            'jenis_kelamin' => $this->jenis_kelamin,
            'alamat' => $this->alamat,
            'default_address' => $defaultAddress ? [
                'address' => $defaultAddress->address,
                'detail_address' => $defaultAddress->detail_address,
                'lat' => $defaultAddress->lat,
                'lng' => $defaultAddress->lng,
            ] : null,
            'preferences' => $this->preference ? [
                'default_parfum' => $this->preference->default_parfum,
                'default_note' => $this->preference->default_note,
                'default_payment_method' => $this->preference->default_payment_method,
            ] : null,
        ];
    }
}
