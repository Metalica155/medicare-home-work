<?php

namespace Tests\Feature\Http\Actions;

use App\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelAppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow(
            CarbonImmutable::parse('2026-07-01 08:00:00')
        );
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_cancels_a_pending_appointment(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::Pending,
        ]);

        $this->patchJson(
            "/api/appointments/{$appointment->id}/cancel",
            [
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'reason' => 'Patient requested cancellation',
            ]
        )->assertNoContent();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Cancelled->value,
            'cancel_reason' => 'Patient requested cancellation',
        ]);
    }

    public function test_cancels_a_confirmed_appointment_more_than_24_hours_before(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::Confirmed,
            'start_time' => '2026-07-03 10:00:00',
            'end_time' => '2026-07-03 10:30:00',
        ]);

        $this->patchJson(
            "/api/appointments/{$appointment->id}/cancel",
            [
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'reason' => 'Unable to attend',
            ]
        )->assertNoContent();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Cancelled->value,
        ]);
    }

    public function test_cannot_cancel_a_confirmed_appointment_within_24_hours(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::Confirmed,
            'start_time' => '2026-07-02 07:00:00',
            'end_time' => '2026-07-02 07:30:00',
        ]);

        $this->patchJson(
            "/api/appointments/{$appointment->id}/cancel",
            [
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'reason' => 'Emergency',
            ]
        )->assertUnprocessable();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Confirmed->value,
            'cancel_reason' => null,
        ]);
    }

    public function test_returns_not_found_when_appointment_does_not_exist(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        $this->patchJson(
            '/api/appointments/999999/cancel',
            [
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'reason' => 'Not needed',
            ]
        )->assertNotFound();
    }

    public function test_returns_unprocessable_when_doctor_or_patient_do_not_match(): void
    {
        $doctor = Doctor::factory()->create();
        $otherDoctor = Doctor::factory()->create();

        $patient = Patient::factory()->create();
        $otherPatient = Patient::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::Pending,
        ]);

        $this->patchJson(
            "/api/appointments/{$appointment->id}/cancel",
            [
                'doctor_id' => $otherDoctor->id,
                'patient_id' => $otherPatient->id,
                'reason' => 'Wrong owner',
            ]
        )->assertForbidden();
    }

    public function test_cannot_cancel_a_completed_appointment(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::Completed,
        ]);

        $this->patchJson(
            "/api/appointments/{$appointment->id}/cancel",
            [
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'reason' => 'Too late',
            ]
        )->assertUnprocessable();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Completed->value,
        ]);
    }
}
