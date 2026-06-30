<?php

namespace App\Domain\Availability\Services;

use App\Domain\Availability\Contracts\SlotGeneratorServiceInterface;
use App\Domain\Availability\ValueObjects\Slot;
use App\Models\Availability;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class SlotGeneratorService implements SlotGeneratorServiceInterface
{
    /**
     * @return Collection<int, Slot>
     */
    public function generateSlots(
        Availability $availability,
        CarbonImmutable $now,
    ): Collection {
        $slots = new Collection();

        $current = CarbonImmutable::parse($availability->starts_at);
        $end = CarbonImmutable::parse($availability->ends_at);

        while (
            ($slotEnd = $current->addMinutes($availability->slot_duration))->lessThanOrEqualTo($end)
        ) {
            if ($current->greaterThan($now)) {
                $slots->push(new Slot(
                    doctorId: $availability->doctor->id,
                    duration: $availability->slot_duration,
                    startsAt: $current,
                    endsAt: $slotEnd,
                ));
            }

            $current = $slotEnd;
        }

        return $slots;
    }
}
