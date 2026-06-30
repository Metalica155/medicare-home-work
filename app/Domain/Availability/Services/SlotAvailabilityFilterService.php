<?php

namespace App\Domain\Availability\Services;

use App\Domain\Availability\Contracts\SlotAvailabilityFilterServiceInterface;
use App\Domain\Availability\ValueObjects\Slot;
use App\Models\Appointment;
use Illuminate\Support\Collection;

class SlotAvailabilityFilterService implements SlotAvailabilityFilterServiceInterface
{
    public function filter(Collection $slots, Collection $appointments): Collection
    {
        return $slots->reject(
            function (Slot $slot) use ($appointments) {
                return $appointments->contains(
                    fn(Appointment $appointment) => $this->overlaps($slot, $appointment)
                );
            }
        )->values();
    }

    private function overlaps(
        Slot $slot,
        Appointment $appointment,
    ): bool {
        return $slot->startsAt->lt($appointment->end_time)
            && $slot->endsAt->gt($appointment->start_time);
    }
}
