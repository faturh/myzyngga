<?php

namespace App\Providers;

use App\Modules\Admin\Domain\Repositories\AdminRepositoryInterface;
use App\Modules\Admin\Infrastructure\Persistence\EloquentAdminRepository;
use App\Modules\Customer\Domain\Repositories\CustomerRepositoryInterface;
use App\Modules\Customer\Infrastructure\Persistence\EloquentCustomerRepository;
use App\Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use App\Modules\Order\Infrastructure\Persistence\EloquentOrderRepository;
use App\Modules\Order\Application\Services\OrderWebService;
use App\Modules\Payment\Domain\Repositories\PaymentRepositoryInterface;
use App\Modules\Payment\Infrastructure\Persistence\EloquentPaymentRepository;
use App\Modules\Transaksi\Domain\Repositories\TransaksiDashboardRepositoryInterface;
use App\Modules\Transaksi\Infrastructure\Persistence\EloquentTransaksiDashboardRepository;
use App\Modules\Transaksi\Domain\Repositories\TimbanganRepositoryInterface;
use App\Modules\Transaksi\Infrastructure\Persistence\EloquentTimbanganRepository;
use App\Modules\Transaksi\Domain\Repositories\KeuanganRepositoryInterface;
use App\Modules\Transaksi\Infrastructure\Persistence\EloquentKeuanganRepository;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
        $this->app->singleton(OrderWebService::class);
        $this->app->bind(CustomerRepositoryInterface::class, EloquentCustomerRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, EloquentPaymentRepository::class);
        $this->app->bind(AdminRepositoryInterface::class, EloquentAdminRepository::class);
        $this->app->bind(TransaksiDashboardRepositoryInterface::class, EloquentTransaksiDashboardRepository::class);
        $this->app->bind(TimbanganRepositoryInterface::class, EloquentTimbanganRepository::class);
        $this->app->bind(KeuanganRepositoryInterface::class, EloquentKeuanganRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('view-order', function (User $user, Transaksi $transaksi) {
            if ($user->isAdmin()) {
                return true;
            }

            return (int) optional($transaksi->pelanggan)->user_id === (int) $user->id;
        });

        Gate::define('manage-order-status', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('verify-payment', function (User $user) {
            return $user->isAdmin();
        });

        if ($this->app->environment('production') || env('VERCEL') == '1') {
            \URL::forceScheme('https');
        }
    }
}
