<?php

namespace App\Domain\Appointment\Contracts;

use App\AppointmentStatus;
use App\Models\Appointment;

interface AppointmentStatusTransitionServiceInterface
{
    public function transitionStatus(
        Appointment $appointment,
        AppointmentStatus $newStatus,
        ?string $reason = null,
    ): Appointment;
}
