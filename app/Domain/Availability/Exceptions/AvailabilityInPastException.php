<?php

namespace App\Domain\Availability\Exceptions;

use App\Domain\Exceptions\DomainException;

class AvailabilityInPastException extends DomainException
{
    protected $message = 'Availability must be in the future.';
}
