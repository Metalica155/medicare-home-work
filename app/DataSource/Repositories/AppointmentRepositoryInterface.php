<?php

namespace App\DataSource\Repositories;

use App\AppointmentStatus;
use App\Domain\Appointment\Commands\CreateAppointmentCommand;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\CarbonImmutable;

interface AppointmentRepositoryInterface
{
    public function create(
        Patient $patient,
        CreateAppointmentCommand $command,
    ): Appointment;

    public function doctorHasAppointment(
        int $doctorId,
        CarbonImmutable $start,
        CarbonImmutable $end,
    ): bool;

    public function patientHasAppointment(
        Patient $patient,
        CarbonImmutable $start,
        CarbonImmutable $end,
    ): bool;

    public function updateStatus(Appointment $appointment, AppointmentStatus $newStatus): Appointment;
}
