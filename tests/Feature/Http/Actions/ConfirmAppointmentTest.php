<?php

namespace Tests\Feature\Http\Actions;

use App\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfirmAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirms_a_pending_appointment(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::Pending,
        ]);

        $response = $this->patchJson(
            "/api/doctors/{$doctor->id}/appointments/{$appointment->id}/confirm"
        );

        $response->assertNoContent();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Confirmed->value,
        ]);
    }

    public function test_returns_not_found_when_appointment_does_not_exist(): void
    {
        $doctor = Doctor::factory()->create();

        $this->patchJson(
            "/api/doctors/{$doctor->id}/appointments/999999/confirm"
        )
            ->assertNotFound();
    }

    public function test_returns_not_found_when_appointment_does_not_belong_to_doctor(): void
    {
        $doctor = Doctor::factory()->create();
        $otherDoctor = Doctor::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $otherDoctor->id,
            'status' => AppointmentStatus::Pending,
        ]);

        $this->patchJson(
            "/api/doctors/{$doctor->id}/appointments/{$appointment->id}/confirm"
        )
            ->assertNotFound();
    }

    public function test_returns_unprocessable_when_transition_is_invalid(): void
    {
        $doctor = Doctor::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'status' => AppointmentStatus::Completed,
        ]);

        $this->patchJson(
            "/api/doctors/{$doctor->id}/appointments/{$appointment->id}/confirm"
        )
            ->assertUnprocessable();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Completed->value,
        ]);
    }

    public function test_cannot_confirm_an_already_confirmed_appointment(): void
    {
        $doctor = Doctor::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'status' => AppointmentStatus::Confirmed,
        ]);

        $this->patchJson(
            "/api/doctors/{$doctor->id}/appointments/{$appointment->id}/confirm"
        )
            ->assertUnprocessable();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Confirmed->value,
        ]);
    }
}
