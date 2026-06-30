<?php

namespace App\Providers;

use App\DataSource\Eloquent\AvailabilityRepository;
use App\DataSource\Eloquent\DoctorRepository;
use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\DataSource\Repositories\DoctorRepositoryInterface;
use App\Domain\Availability\Contracts\CreateAvailabilityServiceInterface;
use App\Domain\Availability\Contracts\ListAvailableSlotsServiceInterface;
use App\Domain\Availability\Contracts\SlotAvailabilityFilterServiceInterface;
use App\Domain\Availability\Contracts\SlotGeneratorServiceInterface;
use App\Domain\Availability\Services\CreateAvailabilityService;
use App\Domain\Availability\Services\ListAvailableSlotsService;
use App\Domain\Availability\Services\SlotAvailabilityFilterService;
use App\Domain\Availability\Services\SlotGeneratorService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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

        $this->app->bind(
            ListAvailableSlotsServiceInterface::class,
            ListAvailableSlotsService::class,
        );

        $this->app->bind(
            SlotGeneratorServiceInterface::class,
            SlotGeneratorService::class,
        );

        $this->app->bind(
            SlotAvailabilityFilterServiceInterface::class,
            SlotAvailabilityFilterService::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Collection::macro('paginate', function ($perPage = 15) {
            $page = LengthAwarePaginator::resolveCurrentPage('page');

            return new LengthAwarePaginator($this->forPage($page, $perPage), $this->count(), $perPage, $page, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]);
        });
    }
}
