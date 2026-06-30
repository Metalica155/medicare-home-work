<?php

namespace App\Domain\Appointment\Contracts;

use App\Models\Doctor;
use Carbon\CarbonImmutable;

interface SlotValidatorServiceInterface
{
    public function validate(
        int $doctorId,
        CarbonImmutable $start,
        CarbonImmutable $end,
    ): bool;
}
