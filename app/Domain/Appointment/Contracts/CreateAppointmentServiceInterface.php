<?php

namespace App\Domain\Appointment\Contracts;

use App\Domain\Appointment\Commands\CreateAppointmentCommand;
use App\Models\Appointment;
use App\Models\Patient;

interface CreateAppointmentServiceInterface
{
    public function create(Patient $patient, CreateAppointmentCommand $command): Appointment;
}
