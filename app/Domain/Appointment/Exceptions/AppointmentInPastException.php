<?php

namespace App\Domain\Appointment\Exceptions;

use App\Domain\Exceptions\DomainException;

class AppointmentInPastException extends DomainException
{
    protected $message = 'Appointment must be in the future.';
}
