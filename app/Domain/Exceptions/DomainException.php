<?php

namespace App\Domain\Exceptions;

use Illuminate\Http\Response;
use RuntimeException;

abstract class DomainException extends RuntimeException
{
    public function statusCode(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
