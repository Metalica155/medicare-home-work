<?php

namespace App\Domain\Availability\Contracts;

use App\Domain\Availability\ValueObjects\Slot;
use App\Models\Availability;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

interface SlotGeneratorServiceInterface
{
    /**
     * @return Collection<int, Slot>
     */
    public function generateSlots(
        Availability $availability,
        CarbonImmutable $now,
    ): Collection;
}
