<?php

namespace App\Http\Actions\Appointments;

use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;

class GetAppointmentAction
{
    public function __invoke(Appointment $appointment): AppointmentResource
    {
        return $appointment->toResource();
    }
}
