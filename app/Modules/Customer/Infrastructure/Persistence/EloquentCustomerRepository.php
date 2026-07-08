<?php

namespace App\Modules\Customer\Infrastructure\Persistence;

use App\Models\CustomerAddress;
use App\Models\Pelanggan;
use App\Models\User;
use App\Modules\Customer\Domain\Repositories\CustomerRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
                    'nama'          => $payload['nama'],
                    'jenis_kelamin' => $payload['jenis_kelamin'] ?? 'L',
                    'telepon'       => $payload['telepon'],
                    'alamat'        => $payload['alamat'] ?? null,
                ],
            );
        }

        // For Guest: Find by phone or create new
        return Pelanggan::query()->updateOrCreate(
            ['telepon' => $payload['telepon']],
            [
                'nama'          => $payload['nama'],
                'jenis_kelamin' => $payload['jenis_kelamin'] ?? 'L',
                'alamat'        => $payload['alamat'] ?? null,
            ]
        );
    }

    /** @deprecated Gunakan storeAddress untuk multi-address */
    public function upsertAddress(Pelanggan $pelanggan, array $payload): CustomerAddress
    {
        return $pelanggan->addresses()->updateOrCreate(
            ['is_default' => true],
            [
                'label'          => $payload['label'] ?? 'Utama',
                'address'        => $payload['address'],
                'detail_address' => $payload['detail_address'] ?? null,
                'lat'            => $payload['lat'] ?? null,
                'lng'            => $payload['lng'] ?? null,
            ],
        );
    }

    public function upsertPreference(Pelanggan $pelanggan, array $payload): \App\Models\CustomerPreference
    {
        return $pelanggan->preference()->updateOrCreate(
            ['pelanggan_id' => $pelanggan->id],
            [
                'default_parfum'         => $payload['default_parfum'] ?? null,
                'default_note'           => $payload['default_note'] ?? null,
                'default_payment_method' => $payload['default_payment_method'] ?? null,
            ],
        );
    }

    // ── Multi-Address CRUD ────────────────────────────────────────────────

    public function listAddresses(Pelanggan $pelanggan): Collection
    {
        return $pelanggan->addresses()
            ->orderByDesc('is_default')
            ->orderBy('created_at')
            ->get();
    }

    public function storeAddress(Pelanggan $pelanggan, array $payload): CustomerAddress
    {
        $isFirst = $pelanggan->addresses()->count() === 0;

        return $pelanggan->addresses()->create([
            'label'          => $payload['label'] ?? 'Alamat Baru',
            'address'        => $payload['address'],
            'detail_address' => $payload['detail_address'] ?? null,
            'lat'            => $payload['lat'] ?? null,
            'lng'            => $payload['lng'] ?? null,
            'is_default'     => $isFirst, // alamat pertama otomatis jadi utama
        ]);
    }

    public function findAddressById(int $addressId): ?CustomerAddress
    {
        return CustomerAddress::find($addressId);
    }

    public function updateAddress(CustomerAddress $address, array $payload): CustomerAddress
    {
        $address->update([
            'label'          => $payload['label'] ?? $address->label,
            'address'        => $payload['address'] ?? $address->address,
            'detail_address' => $payload['detail_address'] ?? $address->detail_address,
            'lat'            => array_key_exists('lat', $payload) ? $payload['lat'] : $address->lat,
            'lng'            => array_key_exists('lng', $payload) ? $payload['lng'] : $address->lng,
        ]);

        return $address->fresh();
    }

    public function deleteAddress(CustomerAddress $address): void
    {
        $address->delete();
    }

    public function setPrimaryAddress(Pelanggan $pelanggan, CustomerAddress $address): void
    {
        DB::transaction(function () use ($pelanggan, $address) {
            $pelanggan->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });
    }
}
