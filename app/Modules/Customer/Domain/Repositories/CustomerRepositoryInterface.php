<?php

namespace App\Modules\Customer\Domain\Repositories;

use App\Models\CustomerAddress;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Support\Collection;

interface CustomerRepositoryInterface
{
    public function findByUser(User $user): ?Pelanggan;

    public function findByPhone(string $phone): ?Pelanggan;

    public function upsertProfile(?User $user, array $payload): Pelanggan;

    /** @deprecated Gunakan storeAddress untuk multi-address */
    public function upsertAddress(Pelanggan $pelanggan, array $payload): CustomerAddress;

    public function upsertPreference(Pelanggan $pelanggan, array $payload): \App\Models\CustomerPreference;

    // ── Multi-Address CRUD ────────────────────────────────────────────────

    public function listAddresses(Pelanggan $pelanggan): Collection;

    public function storeAddress(Pelanggan $pelanggan, array $payload): CustomerAddress;

    public function findAddressById(int $addressId): ?CustomerAddress;

    public function updateAddress(CustomerAddress $address, array $payload): CustomerAddress;

    public function deleteAddress(CustomerAddress $address): void;

    public function setPrimaryAddress(Pelanggan $pelanggan, CustomerAddress $address): void;
}
