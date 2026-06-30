<?php

namespace App\DataSource\Repositories;

use App\Domain\Availability\Commands\CreateAvailabilityCommand;
use App\Domain\Availability\Queries\ListAvailableSlotsQuery;
use App\Models\Availability;
use App\Models\Doctor;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

interface AvailabilityRepositoryInterface
{
    public function create(
        Doctor $doctor,
        CreateAvailabilityCommand $command,
    ): Availability;

    public function overlaps(
        Doctor $doctor,
        CarbonImmutable $startsAt,
        CarbonImmutable $endsAt
    ): bool;

    /**
     * @return Collection<int, Availability>
     */
    public function listAvailabilities(
        ListAvailableSlotsQuery $input,
    ): Collection;

    public function findContainingAvailability(
        int $doctorId,
        CarbonImmutable $startsAt,
        CarbonImmutable $endsAt,
    ): ?Availability;
}
