<?php

namespace App\Http\Actions\Appointments;

use App\AppointmentStatus;
use App\Domain\Appointment\Contracts\AppointmentStatusTransitionServiceInterface;
use App\Http\Actions\Action;
use App\Models\Appointment;

abstract class AppointmentStatusTransitionAction extends Action
{
    public function __construct(
        protected readonly AppointmentStatusTransitionServiceInterface $statusTransitionService,
    ) {}

    protected function transition(Appointment $appointment, AppointmentStatus $newStatus): Appointment
    {
        return $this->statusTransitionService->transitionStatus($appointment, $newStatus);
    }
}
