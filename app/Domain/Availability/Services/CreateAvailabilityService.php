<?php

namespace App\Domain\Availability\Services;

use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Availability\Commands\CreateAvailabilityCommand;
use App\Domain\Availability\Contracts\CreateAvailabilityServiceInterface;
use App\Domain\Availability\Exceptions\AvailabilityInPastException;
use App\Domain\Availability\Exceptions\AvailabilityOverlapException;
use App\Models\Availability;
use App\Models\Doctor;

class CreateAvailabilityService implements CreateAvailabilityServiceInterface
{
    public function __construct(
        private AvailabilityRepositoryInterface $repository,
    ) {}

    public function create(Doctor $doctor, CreateAvailabilityCommand $command): Availability
    {
        $this->ensureStartsInFuture($command);

        $this->ensureDoesNotOverlap($doctor, $command);

        return $this->repository->create($doctor, $command);
    }

    private function ensureDoesNotOverlap(Doctor $doctor, CreateAvailabilityCommand $command): void
    {
        if ($this->repository->overlaps(
            $doctor,
            $command->startsAt,
            $command->endsAt
        ) === true) {
            throw new AvailabilityOverlapException();
        }
    }

    private function ensureStartsInFuture(CreateAvailabilityCommand $command): void
    {
        if ($command->startsAt->isPast()) {
            throw new AvailabilityInPastException();
        }
    }
}
