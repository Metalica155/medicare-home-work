<?php

namespace App\Domain\Availability\Contracts;

use App\Domain\Availability\ValueObjects\Slot;
use App\Models\Appointment;
use Illuminate\Support\Collection;

interface SlotAvailabilityFilterServiceInterface
{
    /**
     * @param Collection<int, Slot> $slots
     * @param Collection<int, Appointment> $appointments
     *
     * @return Collection<int, Slot>
     */
    public function filter(Collection $slots, Collection $appointments): Collection;
}
