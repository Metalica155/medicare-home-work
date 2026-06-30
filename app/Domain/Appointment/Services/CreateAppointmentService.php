<?php

namespace App\Domain\Appointment\Services;

use App\DataSource\Repositories\AppointmentRepositoryInterface;
use App\Domain\Appointment\Commands\CreateAppointmentCommand;
use App\Domain\Appointment\Contracts\CreateAppointmentServiceInterface;
use App\Domain\Appointment\Contracts\SlotValidatorServiceInterface;
use App\Domain\Appointment\Exceptions\AppointmentInPastException;
use App\Domain\Appointment\Exceptions\DoctorAlreadyHaveAppointmentException;
use App\Domain\Appointment\Exceptions\InvalidSlotException;
use App\Domain\Appointment\Exceptions\PatientAlreadyHaveAppointmentException;
use App\Models\Appointment;
use App\Models\Patient;

class CreateAppointmentService implements CreateAppointmentServiceInterface
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $repository,
        private readonly SlotValidatorServiceInterface $slotValidator,
    ) {}

    public function create(Patient $patient, CreateAppointmentCommand $command): Appointment
    {
        $this->ensureStartsInFuture($command);
        $this->isDoctorAvailable($command);
        $this->isPatientAvailable($patient, $command);
        $this->isSlotAvailable($command);

        return $this->repository->create($patient, $command);
    }

    private function ensureStartsInFuture(CreateAppointmentCommand $command): void
    {
        if ($command->startTime->isPast()) {
            throw new AppointmentInPastException();
        }
    }

    private function isDoctorAvailable(CreateAppointmentCommand $command): void
    {
        if ($this->repository->doctorHasAppointment(
            $command->doctorId,
            $command->startTime,
            $command->endTime
        )) {
            throw new DoctorAlreadyHaveAppointmentException();
        }
    }

    private function isPatientAvailable(Patient $patient, CreateAppointmentCommand $command): void
    {
        if ($this->repository->patientHasAppointment(
            $patient,
            $command->startTime,
            $command->endTime
        )) {
            throw new PatientAlreadyHaveAppointmentException();
        }
    }

    private function isSlotAvailable(CreateAppointmentCommand $command): void
    {
        if ($this->slotValidator->validate(
            $command->doctorId,
            $command->startTime,
            $command->endTime
        ) === false) {
            throw new InvalidSlotException();
        }
    }
}
