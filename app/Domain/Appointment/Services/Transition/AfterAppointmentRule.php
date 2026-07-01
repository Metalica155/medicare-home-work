<?php

namespace App\Domain\Appointment\Services\Transition;

use App\Domain\Appointment\Exceptions\AppointmentNotInPastException;
use App\Models\Appointment;
use Carbon\CarbonImmutable;

class AfterAppointmentRule implements Rule
{
    public function validate(Appointment $appointment): void
    {
        if (
            CarbonImmutable::parse($appointment->end_time)->lte(CarbonImmutable::now()) === false
        ) {
            throw new AppointmentNotInPastException();
        }
    }
}
