<?php

namespace App\Domain\Appointment\Services\Transition;

use App\Models\Appointment;

interface Rule
{
    public function validate(Appointment $appointment): void;
}
