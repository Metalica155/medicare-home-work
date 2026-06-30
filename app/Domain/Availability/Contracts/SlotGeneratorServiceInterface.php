<?php

namespace App\Domain\Availability\Contracts;

use App\Domain\Availability\ValueObjects\Slot;
use App\Models\Availability;
use Carbon\CarbonImmutable;

interface SlotGeneratorServiceInterface
{
    /**
     * @return Slot[]
     */
    public function generateSlots(
        Availability $availability,
        CarbonImmutable $now,
    ): array;
}
