<?php

namespace App\Domain\Appointment\Exceptions;

use App\Domain\Exceptions\DomainException;

class AppointmentNotInPastException extends DomainException
{
    protected $message = 'Appointment is not in the past.';
}
