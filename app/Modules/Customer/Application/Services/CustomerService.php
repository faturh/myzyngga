<?php

namespace App\Modules\Customer\Application\Services;

use App\Models\CustomerAddress;
use App\Models\User;
use App\Modules\Customer\Domain\Repositories\CustomerRepositoryInterface;
use App\Shared\Exceptions\DomainException;
use Illuminate\Support\Collection;

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

    public function upsertAddress(User $user, array $payload): CustomerAddress
    {
        $profile = $this->repository->upsertProfile($user, [
            'nama'          => $user->name,
            'jenis_kelamin' => $payload['jenis_kelamin'] ?? 'L',
            'telepon'       => $payload['telepon'] ?? '-',
            'alamat'        => $payload['address'],
        ]);

        return $this->repository->upsertAddress($profile, $payload);
    }

    public function upsertPreference(User $user, array $payload): \App\Models\CustomerPreference
    {
        $profile = $this->repository->upsertProfile($user, [
            'nama'          => $user->name,
            'jenis_kelamin' => $payload['jenis_kelamin'] ?? 'L',
            'telepon'       => $payload['telepon'] ?? '-',
            'alamat'        => null,
        ]);

        return $this->repository->upsertPreference($profile, $payload);
    }

    // ── Multi-Address CRUD ────────────────────────────────────────────────

    public function listAddresses(User $user): Collection
    {
        $pelanggan = $this->resolvePelanggan($user);

        return $this->repository->listAddresses($pelanggan);
    }

    public function storeAddress(User $user, array $payload): CustomerAddress
    {
        $pelanggan = $this->resolvePelanggan($user);

        if ($this->repository->listAddresses($pelanggan)->count() >= 3) {
            throw new DomainException('Kamu hanya dapat menyimpan maksimal 3 alamat.', 422);
        }

        return $this->repository->storeAddress($pelanggan, $payload);
    }

    public function updateAddressForUser(User $user, int $addressId, array $payload): CustomerAddress
    {
        $pelanggan = $this->resolvePelanggan($user);
        $address   = $this->resolveOwnedAddress($addressId, $pelanggan->id);

        return $this->repository->updateAddress($address, $payload);
    }

    public function deleteAddressForUser(User $user, int $addressId): void
    {
        $pelanggan = $this->resolvePelanggan($user);
        $address   = $this->resolveOwnedAddress($addressId, $pelanggan->id);

        if ($address->is_default) {
            throw new DomainException(
                'Tidak dapat menghapus alamat utama. Tetapkan alamat lain sebagai utama terlebih dahulu.',
                422
            );
        }

        $this->repository->deleteAddress($address);
    }

    public function setPrimaryForUser(User $user, int $addressId): CustomerAddress
    {
        $pelanggan = $this->resolvePelanggan($user);
        $address   = $this->resolveOwnedAddress($addressId, $pelanggan->id);

        $this->repository->setPrimaryAddress($pelanggan, $address);

        return $address->fresh();
    }

    // ── Private Helpers ───────────────────────────────────────────────────

    private function resolvePelanggan(User $user): \App\Models\Pelanggan
    {
        $pelanggan = $this->repository->findByUser($user);
        if (! $pelanggan) {
            throw new DomainException('Profil pelanggan belum tersedia.', 404);
        }

        return $pelanggan;
    }

    /**
     * Pastikan alamat ada dan milik pelanggan yang login (IDOR protection).
     *
     * @throws DomainException 403 jika bukan pemilik
     */
    private function resolveOwnedAddress(int $addressId, int $pelangganId): CustomerAddress
    {
        $address = $this->repository->findAddressById($addressId);

        if (! $address || (int) $address->pelanggan_id !== $pelangganId) {
            throw new DomainException('Alamat tidak ditemukan atau akses ditolak.', 403);
        }

        return $address;
    }
}
