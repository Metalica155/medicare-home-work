<?php

namespace App\Domain\Appointment\Services\Transition;

use App\Domain\Appointment\Exceptions\AppointmentBefore24HoursException;
use App\Models\Appointment;
use Carbon\CarbonImmutable;

class Before24HoursRule implements Rule
{
    public function validate(Appointment $appointment): void
    {
        $cutoff = CarbonImmutable::now()->addHours(24);

        if (
            CarbonImmutable::parse($appointment->start_time)->gte($cutoff) === false
        ) {
            throw new AppointmentBefore24HoursException();
        }
    }
}
