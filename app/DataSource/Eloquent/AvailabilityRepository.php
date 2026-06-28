<?php

namespace App\DataSource\Eloquent;

use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Availability\Commands\CreateAvailabilityCommand;
use App\Models\Availability;
use App\Models\Doctor;
use Carbon\CarbonImmutable;

class AvailabilityRepository implements AvailabilityRepositoryInterface
{
    public function create(
        Doctor $doctor,
        CreateAvailabilityCommand $command,
    ): Availability {
        return $doctor->availabilities()->create([
            'starts_at'     => $command->startsAt,
            'ends_at'       => $command->endsAt,
            'slot_duration' => $command->slotDuration,
        ]);
    }

    public function overlaps(Doctor $doctor, CarbonImmutable $startsAt, CarbonImmutable $endsAt): bool
    {
        return $doctor->availabilities()
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();
    }
}
