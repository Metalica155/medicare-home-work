<?php

namespace App\Domain\Availability\Bridges;

use App\Domain\Appointment\Contracts\SlotValidatorServiceInterface;
use App\Domain\Availability\Services\SlotValidatorService;
use Carbon\CarbonImmutable;

class SlotValidatorServiceBridge implements SlotValidatorServiceInterface
{
    public function __construct(
        private readonly SlotValidatorService $service
    ) {}

    public function validate(
        int $doctorId,
        CarbonImmutable $start,
        CarbonImmutable $end,
    ): bool {
        return $this->service->validate($doctorId, $start, $end);
    }
}
