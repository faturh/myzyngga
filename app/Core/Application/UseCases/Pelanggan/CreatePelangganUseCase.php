<?php

namespace App\Core\Application\UseCases\Pelanggan;

use App\Core\Domain\Repositories\PelangganRepositoryInterface;
use App\Models\Pelanggan;

class CreatePelangganUseCase
{
    private $repository;

    public function __construct(PelangganRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data): Pelanggan
    {
        return $this->repository->create($data);
    }
}
