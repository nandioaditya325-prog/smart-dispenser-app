<?php

namespace App\Providers;

use App\Repositories\Contracts\DeviceRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Eloquent\DeviceRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\PaymentRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Binds repository contracts to their Eloquent implementations so
 * services depend on interfaces (clean architecture / testability)
 * rather than concrete Eloquent classes.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(DeviceRepositoryInterface::class, DeviceRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
    }
}
