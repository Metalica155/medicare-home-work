<?php

namespace App\Domain\Availability\Exceptions;

use App\Domain\Exceptions\DomainException;
use Illuminate\Http\Response;

class AvailabilityOverlapException extends DomainException
{
    protected $message = 'The availability overlaps with an existing availability.';

    public function statusCode(): int
    {
        return Response::HTTP_CONFLICT;
    }
}
