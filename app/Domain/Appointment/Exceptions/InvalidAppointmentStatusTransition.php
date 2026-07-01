<?php

namespace App\Domain\Appointment\Exceptions;

use App\Domain\Exceptions\DomainException;

class InvalidAppointmentStatusTransition extends DomainException
{
    protected $message = 'Appointment new status is invalid.';
}
