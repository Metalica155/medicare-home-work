<?php

namespace App\Http\Actions\Appointments;

use App\Domain\Appointment\Commands\CreateAppointmentCommand;
use App\Domain\Appointment\Contracts\CreateAppointmentServiceInterface;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Patient;

class StoreAppointmentAction
{
    public function __construct(
        private readonly CreateAppointmentServiceInterface $service,
    ) {}

    public function __invoke(Patient $patient, StoreAppointmentRequest $request): AppointmentResource
    {
        return $this->service->create(
            $patient,
            CreateAppointmentCommand::fromArray($request->validated())
        )->toResource();
    }
}
