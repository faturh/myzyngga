<?php

namespace App\Core\Application\UseCases\Transaksi;

use App\Core\Domain\Repositories\TransaksiRepositoryInterface;
use Illuminate\Support\Collection;

class GetTransaksiByCabangUseCase
{
    private $repository;

    public function __construct(TransaksiRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $cabangId): Collection
    {
        return $this->repository->getAllByCabang($cabangId);
    }
}
