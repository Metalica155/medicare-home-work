<?php

namespace Tests\Feature\Http\Actions;

use App\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompleteAppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow(
            CarbonImmutable::parse('2026-07-02 10:00:00')
        );
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_completes_a_confirmed_appointment(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => AppointmentStatus::Confirmed,
            'start_time' => '2026-07-02 08:00:00',
            'end_time' => '2026-07-02 08:30:00',
        ]);

        $response = $this->patchJson(
            "/api/doctors/{$doctor->id}/appointments/{$appointment->id}/completed"
        );

        $response->assertNoContent();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Completed->value,
        ]);
    }

    public function test_returns_not_found_when_appointment_does_not_exist(): void
    {
        $doctor = Doctor::factory()->create();

        $this->patchJson(
            "/api/doctors/{$doctor->id}/appointments/999999/completed"
        )->assertNotFound();
    }

    public function test_returns_not_found_when_appointment_does_not_belong_to_doctor(): void
    {
        $doctor = Doctor::factory()->create();
        $otherDoctor = Doctor::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $otherDoctor->id,
            'status' => AppointmentStatus::Confirmed,
        ]);

        $this->patchJson(
            "/api/doctors/{$doctor->id}/appointments/{$appointment->id}/completed"
        )->assertNotFound();
    }

    public function test_returns_unprocessable_when_transition_is_invalid(): void
    {
        $doctor = Doctor::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'status' => AppointmentStatus::Pending,
            'start_time' => '2026-07-02 08:00:00',
            'end_time' => '2026-07-02 08:30:00',
        ]);

        $this->patchJson(
            "/api/doctors/{$doctor->id}/appointments/{$appointment->id}/completed"
        )->assertUnprocessable();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Pending->value,
        ]);
    }

    public function test_returns_unprocessable_when_appointment_has_not_happened_yet(): void
    {
        $doctor = Doctor::factory()->create();

        $appointment = Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'status' => AppointmentStatus::Confirmed,
            'start_time' => '2026-07-02 12:00:00',
            'end_time' => '2026-07-02 12:30:00',
        ]);

        $this->patchJson(
            "/api/doctors/{$doctor->id}/appointments/{$appointment->id}/completed"
        )->assertUnprocessable();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::Confirmed->value,
        ]);
    }
}
