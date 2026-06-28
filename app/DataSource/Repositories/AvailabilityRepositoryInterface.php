<?php

namespace App\DataSource\Repositories;

use App\Domain\Availability\Commands\CreateAvailabilityCommand;
use App\Models\Availability;
use App\Models\Doctor;
use Carbon\CarbonImmutable;

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
}
