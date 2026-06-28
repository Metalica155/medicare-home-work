<?php

namespace App\Domain\Availability\Contracts;

use App\Domain\Availability\Commands\CreateAvailabilityCommand;
use App\Models\Availability;
use App\Models\Doctor;

interface CreateAvailabilityServiceInterface
{
    public function create(Doctor $doctor, CreateAvailabilityCommand $data): Availability;
}
