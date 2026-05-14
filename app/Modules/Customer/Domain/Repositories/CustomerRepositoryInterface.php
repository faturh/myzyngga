<?php

namespace App\Modules\Customer\Domain\Repositories;

use App\Models\Pelanggan;
use App\Models\User;

interface CustomerRepositoryInterface
{
    public function findByUser(User $user): ?Pelanggan;

    public function findByPhone(string $phone): ?Pelanggan;

    public function upsertProfile(?User $user, array $payload): Pelanggan;

    public function upsertAddress(Pelanggan $pelanggan, array $payload): \App\Models\CustomerAddress;

    public function upsertPreference(Pelanggan $pelanggan, array $payload): \App\Models\CustomerPreference;
}
