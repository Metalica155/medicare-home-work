<?php

namespace Tests\Feature\Http\Actions;

use App\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAppointmentTest extends TestCase
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

    public function test_creates_an_appointment(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        Availability::factory()->create([
            'doctor_id'     => $doctor->id,
            'starts_at'     => '2026-07-02 08:00:00',
            'ends_at'       => '2026-07-02 10:00:00',
            'slot_duration' => 30,
        ]);

        $response = $this->postJson(
            "/api/patients/{$patient->id}/appointments",
            [
                'doctor_id'  => $doctor->id,
                'start_time' => '2026-07-02 08:00:00',
                'end_time'   => '2026-07-02 08:30:00',
            ]
        );

        $response
            ->assertCreated()
            ->assertJsonPath('data.doctor.id', $doctor->id)
            ->assertJsonPath('data.patient.id', $patient->id);

        $this->assertDatabaseHas('appointments', [
            'doctor_id'  => $doctor->id,
            'patient_id' => $patient->id,
            'start_time' => '2026-07-02 08:00:00',
            'end_time'   => '2026-07-02 08:30:00',
            'status'     => AppointmentStatus::Pending->value,
        ]);
    }

    public function test_validates_the_request(): void
    {
        $patient = Patient::factory()->create();

        $this->postJson(
            "/api/patients/{$patient->id}/appointments",
            []
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'doctor_id',
                'start_time',
                'end_time',
            ]);
    }

    public function test_returns_validation_error_when_appointment_is_in_the_past(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        $this->postJson(
            "/api/patients/{$patient->id}/appointments",
            [
                'doctor_id'  => $doctor->id,
                'start_time' => '2026-06-30 08:00:00',
                'end_time'   => '2026-06-30 08:30:00',
            ]
        )->assertJsonValidationErrors([
            'start_time'
        ]);
    }

    public function test_returns_bad_request_when_slot_is_invalid(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        Availability::factory()->create([
            'doctor_id'     => $doctor->id,
            'starts_at'     => '2026-07-02 08:00:00',
            'ends_at'       => '2026-07-02 10:00:00',
            'slot_duration' => 30,
        ]);

        $this->postJson(
            "/api/patients/{$patient->id}/appointments",
            [
                'doctor_id'  => $doctor->id,
                'start_time' => '2026-07-02 08:15:00',
                'end_time'   => '2026-07-02 08:45:00',
            ]
        )->assertUnprocessable();
    }

    public function test_returns_conflict_when_doctor_already_has_an_appointment(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        Appointment::factory()->create([
            'doctor_id'  => $doctor->id,
            'patient_id' => Patient::factory(),
            'start_time' => '2026-07-02 08:00:00',
            'end_time'   => '2026-07-02 08:30:00',
            'status'     => AppointmentStatus::Pending,
        ]);

        Availability::factory()->create([
            'doctor_id'     => $doctor->id,
            'starts_at'     => '2026-07-02 08:00:00',
            'ends_at'       => '2026-07-02 10:00:00',
            'slot_duration' => 30,
        ]);

        $this->postJson(
            "/api/patients/{$patient->id}/appointments",
            [
                'doctor_id' => $doctor->id,
                'start_time' => '2026-07-02 08:00:00',
                'end_time' => '2026-07-02 08:30:00',
            ]
        )->assertConflict();
    }

    public function test_returns_conflict_when_patient_already_has_an_appointment(): void
    {
        $doctor = Doctor::factory()->create();
        $otherDoctor = Doctor::factory()->create();

        $patient = Patient::factory()->create();

        Appointment::factory()->create([
            'doctor_id'  => $doctor->id,
            'patient_id' => $patient->id,
            'start_time' => '2026-07-02 08:00:00',
            'end_time'   => '2026-07-02 08:30:00',
            'status'     => AppointmentStatus::Pending,
        ]);

        Availability::factory()->create([
            'doctor_id'     => $otherDoctor->id,
            'starts_at'     => '2026-07-02 08:00:00',
            'ends_at'       => '2026-07-02 10:00:00',
            'slot_duration' => 30,
        ]);

        $this->postJson(
            "/api/patients/{$patient->id}/appointments",
            [
                'doctor_id'  => $otherDoctor->id,
                'start_time' => '2026-07-02 08:00:00',
                'end_time'   => '2026-07-02 08:30:00',
            ]
        )->assertConflict();
    }
}
