<?php

namespace App\Core\Application\UseCases\Pelanggan;

use App\Core\Domain\Repositories\PelangganRepositoryInterface;
use Illuminate\Support\Collection;

class GetAllPelangganUseCase
{
    private $repository;

    public function __construct(PelangganRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): Collection
    {
        return $this->repository->getAll();
    }
}
