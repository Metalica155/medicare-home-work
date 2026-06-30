<?php

namespace App\Domain\Availability\Services;

use App\Domain\Availability\Contracts\SlotGeneratorServiceInterface;
use App\Domain\Availability\ValueObjects\Slot;
use App\Models\Availability;
use Carbon\CarbonImmutable;

class SlotGeneratorService implements SlotGeneratorServiceInterface
{
    /**
     * @return Slot[]
     */
    public function generateSlots(
        Availability $availability,
        CarbonImmutable $now,
    ): array {
        $slots = [];

        $current = CarbonImmutable::parse($availability->starts_at);
        $end = CarbonImmutable::parse($availability->ends_at);

        while (
            ($slotEnd = $current->addMinutes($availability->slot_duration))->lessThanOrEqualTo($end)
        ) {
            if ($current->greaterThan($now)) {
                $slots[] = new Slot(
                    doctorId: $availability->doctor->id,
                    duration: $availability->slot_duration,
                    startsAt: $current,
                    endsAt: $slotEnd,
                );
            }

            $current = $slotEnd;
        }

        return $slots;
    }
}
