<?php

namespace App\DataSource\Eloquent;

use App\AppointmentStatus;
use App\DataSource\Repositories\AppointmentRepositoryInterface;
use App\Domain\Appointment\Commands\CreateAppointmentCommand;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\CarbonImmutable;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function create(
        Patient $patient,
        CreateAppointmentCommand $command,
    ): Appointment {
        $appointment = $patient->appointments()->create(
            [
                'doctor_id'  => $command->doctorId,
                'start_time' => $command->startTime,
                'end_time'   => $command->endTime,
            ]
        );

        return $appointment;
    }

    public function doctorHasAppointment(
        int $doctorId,
        CarbonImmutable $start,
        CarbonImmutable $end,
    ): bool {
        return Doctor::find($doctorId)->appointments()
            ->where('status', '!=', AppointmentStatus::Cancelled)
            ->where('status', '!=', AppointmentStatus::Completed)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();
    }

    public function patientHasAppointment(
        Patient $patient,
        CarbonImmutable $start,
        CarbonImmutable $end,
    ): bool {
        return $patient->appointments()
            ->where('status', '!=', AppointmentStatus::Cancelled)
            ->where('status', '!=', AppointmentStatus::Completed)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();
    }

    public function updateStatus(
        Appointment $appointment,
        AppointmentStatus $newStatus,
    ): Appointment {
        $appointment->update(['status' => $newStatus]);
        $appointment->save();

        return $appointment;
    }

    public function cancel(
        Appointment $appointment,
        ?string $reason = null,
    ): Appointment {
        $appointment->update(
            [
                'status'        => AppointmentStatus::Cancelled,
                'cancel_reason' => $reason,
            ]
        );
        $appointment->save();

        return $appointment;
    }
}
