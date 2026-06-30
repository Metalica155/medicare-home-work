<?php

namespace Database\Factories;

use App\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 day', '+1 month');
        $end = (clone $start)->modify('+30 minutes');

        return [
            'start_time'    => $start,
            'end_time'      => $end,
            'status'        => AppointmentStatus::Pending,
            'cancel_reason' => null,
            'doctor_id'     => Doctor::factory(),
            'patient_id'    => Patient::factory(),
        ];
    }

    public function cancelled(?string $reason = null): static
    {
        return $this->state(fn() => [
            'status'        => AppointmentStatus::Cancelled,
            'cancel_reason' => $reason ?? fake()->sentence(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn() => [
            'status'        => AppointmentStatus::Completed,
            'cancel_reason' => null,
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn() => [
            'status'        => AppointmentStatus::Confirmed,
            'cancel_reason' => null,
        ]);
    }
}
