<?php

namespace App\Domain\Appointment\Exceptions;

use App\Domain\Exceptions\DomainException;
use Illuminate\Http\Response;

class PatientAlreadyHaveAppointmentException extends DomainException
{
    protected $message = 'Patient already have appointment in this slot.';

    public function statusCode(): int
    {
        return Response::HTTP_CONFLICT;
    }
}
