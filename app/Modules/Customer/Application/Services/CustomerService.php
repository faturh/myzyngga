<?php

namespace App\Modules\Customer\Application\Services;

use App\Models\User;
use App\Modules\Customer\Domain\Repositories\CustomerRepositoryInterface;
use App\Shared\Exceptions\DomainException;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $repository,
    ) {
    }

    public function getProfile(User $user): \App\Models\Pelanggan
    {
        $profile = $this->repository->findByUser($user);
        if (! $profile) {
            throw new DomainException('Profil pelanggan belum tersedia.', 404);
        }

        return $profile;
    }

    public function upsertAddress(User $user, array $payload): \App\Models\CustomerAddress
    {
        $profile = $this->repository->upsertProfileForUser($user, [
            'nama' => $user->name,
            'jenis_kelamin' => $payload['jenis_kelamin'] ?? 'L',
            'telepon' => $payload['telepon'] ?? '-',
            'alamat' => $payload['address'],
        ]);

        return $this->repository->upsertAddress($profile, $payload);
    }

    public function upsertPreference(User $user, array $payload): \App\Models\CustomerPreference
    {
        $profile = $this->repository->upsertProfileForUser($user, [
            'nama' => $user->name,
            'jenis_kelamin' => $payload['jenis_kelamin'] ?? 'L',
            'telepon' => $payload['telepon'] ?? '-',
            'alamat' => null,
        ]);

        return $this->repository->upsertPreference($profile, $payload);
    }
}
