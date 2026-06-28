<?php

namespace App\Domain\Availability\Services;

use App\Domain\Availability\Commands\CreateAvailabilityCommand;
use App\Domain\Availability\Contracts\CreateAvailabilityServiceInterface;
use App\Domain\Availability\Exceptions\AvailabilityInPastException;
use App\Domain\Availability\Exceptions\AvailabilityOverlapException;
use App\Models\Availability;
use App\Models\Doctor;

class CreateAvailabilityService implements CreateAvailabilityServiceInterface
{
    public function create(Doctor $doctor, CreateAvailabilityCommand $data): Availability
    {
        $this->ensureStartsInFuture($data);

        $this->ensureDoesNotOverlap($doctor, $data);

        return $doctor->availabilities()->create($data->toArray());
    }

    private function ensureDoesNotOverlap(Doctor $doctor, CreateAvailabilityCommand $data): void
    {
        $overlaps = $doctor->availabilities()
            ->where('starts_at', '<', $data->endsAt)
            ->where('ends_at', '>', $data->startsAt)
            ->exists();

        if ($overlaps === true) {
            throw new AvailabilityOverlapException();
        }
    }

    private function ensureStartsInFuture(CreateAvailabilityCommand $data): void
    {
        if ($data->startsAt->isPast()) {
            throw new AvailabilityInPastException();
        }
    }
}
