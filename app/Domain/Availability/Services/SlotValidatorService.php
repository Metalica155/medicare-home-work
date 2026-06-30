<?php

namespace App\Domain\Availability\Services;

use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Availability\Contracts\SlotGeneratorServiceInterface;
use Carbon\CarbonImmutable;

class SlotValidatorService
{
    public function __construct(
        private readonly SlotGeneratorServiceInterface $service,
        private readonly AvailabilityRepositoryInterface $repository,
    ) {}

    public function validate(
        int $doctorId,
        CarbonImmutable $start,
        CarbonImmutable $end,
    ): bool {
        $availability = $this->repository->findContainingAvailability($doctorId, $start, $end);

        if ($availability === null) {
            return false;
        }

        $slots = $this->service->generateSlots(
            $availability,
            CarbonImmutable::now(),
        );

        foreach ($slots as $slot) {
            if ($slot->startsAt->equalTo($start) && $slot->endsAt->equalTo($end)) {
                return true;
            }
        }

        return false;
    }
}
