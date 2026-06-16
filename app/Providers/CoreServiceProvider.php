<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Core\Domain\Repositories\PelangganRepositoryInterface;
use App\Core\Infrastructure\Persistence\Eloquent\EloquentPelangganRepository;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Core\Domain\Repositories\PelangganRepositoryInterface::class,
            \App\Core\Infrastructure\Persistence\Eloquent\EloquentPelangganRepository::class
        );

        $this->app->bind(
            \App\Core\Domain\Repositories\TransaksiRepositoryInterface::class,
            \App\Core\Infrastructure\Persistence\Eloquent\EloquentTransaksiRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
