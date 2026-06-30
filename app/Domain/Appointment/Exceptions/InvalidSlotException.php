<?php

namespace App\Domain\Appointment\Exceptions;

use App\Domain\Exceptions\DomainException;

class InvalidSlotException extends DomainException
{
    protected $message = 'Appointment slot is invalid.';
}
