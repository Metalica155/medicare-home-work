<?php

namespace App\Domain\Appointment\Services;

use App\AppointmentStatus;
use App\DataSource\Eloquent\AppointmentRepository;
use App\Domain\Appointment\Contracts\AppointmentStatusTransitionServiceInterface;
use App\Domain\Appointment\Services\Transition\TransitionValidator;
use App\Models\Appointment;

class AppointmentStatusTransitionService implements AppointmentStatusTransitionServiceInterface
{
    public function __construct(
        private readonly TransitionValidator $transitionValidator,
        private readonly AppointmentRepository $repository,
    ) {}

    public function transitionStatus(
        Appointment $appointment,
        AppointmentStatus $newStatus,
        ?string $reason = null,
    ): Appointment {
        $this->transitionValidator->validate($appointment, $newStatus);

        if ($newStatus === AppointmentStatus::Cancelled) {
            return $this->repository->cancel($appointment, $reason);
        }

        return $this->repository->updateStatus($appointment, $newStatus);
    }
}
