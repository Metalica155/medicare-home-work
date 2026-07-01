<?php

namespace App\Http\Actions\Appointments;

use App\AppointmentStatus;
use App\Http\Requests\CancelAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\Response;

class CancelAppointmentAction extends AppointmentStatusTransitionAction
{
    public function __invoke(Appointment $appointment, CancelAppointmentRequest $request): Response
    {
        $this->transition($appointment, AppointmentStatus::Cancelled, $request->validated('reason'));

        return response()->noContent();
    }
}
