<?php

namespace App\Domain\Appointment\Exceptions;

use App\Domain\Exceptions\DomainException;

class AppointmentBefore24HoursException extends DomainException
{
    protected $message = 'Appointment is not at least 24 hours from now.';
}
