<?php

namespace App\Modules\Customer\Infrastructure\Persistence;

use App\Models\Pelanggan;
use App\Models\User;
use App\Modules\Customer\Domain\Repositories\CustomerRepositoryInterface;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function findByUser(User $user): ?Pelanggan
    {
        return Pelanggan::query()
            ->with(['addresses', 'preference'])
            ->where('user_id', $user->id)
            ->first();
    }

    public function findByPhone(string $phone): ?Pelanggan
    {
        return Pelanggan::query()->where('telepon', $phone)->first();
    }

    public function upsertProfile(?User $user, array $payload): Pelanggan
    {
        if ($user) {
            return Pelanggan::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $payload['nama'],
                    'jenis_kelamin' => $payload['jenis_kelamin'] ?? 'L',
                    'telepon' => $payload['telepon'],
                    'alamat' => $payload['alamat'] ?? null,
                ],
            );
        }

        // For Guest: Find by phone or create new
        return Pelanggan::query()->updateOrCreate(
            ['telepon' => $payload['telepon']],
            [
                'nama' => $payload['nama'],
                'jenis_kelamin' => $payload['jenis_kelamin'] ?? 'L',
                'alamat' => $payload['alamat'] ?? null,
            ]
        );
    }

    public function upsertAddress(Pelanggan $pelanggan, array $payload): \App\Models\CustomerAddress
    {
        return $pelanggan->addresses()->updateOrCreate(
            ['is_default' => true],
            [
                'label' => $payload['label'] ?? 'Utama',
                'address' => $payload['address'],
                'detail_address' => $payload['detail_address'] ?? null,
                'lat' => $payload['lat'] ?? null,
                'lng' => $payload['lng'] ?? null,
            ],
        );
    }

    public function upsertPreference(Pelanggan $pelanggan, array $payload): \App\Models\CustomerPreference
    {
        return $pelanggan->preference()->updateOrCreate(
            ['pelanggan_id' => $pelanggan->id],
            [
                'default_parfum' => $payload['default_parfum'] ?? null,
                'default_note' => $payload['default_note'] ?? null,
                'default_payment_method' => $payload['default_payment_method'] ?? null,
            ],
        );
    }
}
