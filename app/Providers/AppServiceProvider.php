<?php

namespace App\Providers;

use App\DataSource\Eloquent\AvailabilityRepository;
use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Availability\Contracts\CreateAvailabilityServiceInterface;
use App\Domain\Availability\Services\CreateAvailabilityService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CreateAvailabilityServiceInterface::class,
            CreateAvailabilityService::class,
        );

        $this->app->bind(
            AvailabilityRepositoryInterface::class,
            AvailabilityRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
