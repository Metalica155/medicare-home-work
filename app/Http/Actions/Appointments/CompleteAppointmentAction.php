<?php

namespace App\Http\Actions\Appointments;

use App\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Response;

class CompleteAppointmentAction extends AppointmentStatusTransitionAction
{
    public function __invoke(Doctor $doctor, Appointment $appointment): Response
    {
        $this->transition($appointment, AppointmentStatus::Completed);

        return response()->noContent();
    }
}
