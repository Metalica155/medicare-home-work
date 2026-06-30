<?php

namespace Tests\Feature\Http\Actions;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_patient_appointments(): void
    {
        $patient = Patient::factory()->create();

        Appointment::factory()
            ->count(3)
            ->for($patient)
            ->create();

        $response = $this->getJson(
            "/api/patients/{$patient->id}/appointments"
        );

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_filters_by_doctor(): void
    {
        $patient = Patient::factory()->create();

        $doctor = Doctor::factory()->create();
        $otherDoctor = Doctor::factory()->create();

        Appointment::factory()
            ->count(2)
            ->for($patient)
            ->for($doctor)
            ->create();

        Appointment::factory()
            ->count(3)
            ->for($patient)
            ->for($otherDoctor)
            ->create();

        $response = $this->getJson(
            "/api/patients/{$patient->id}/appointments?doctor_id={$doctor->id}"
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_filters_by_from_and_to(): void
    {
        $patient = Patient::factory()->create();

        Appointment::factory()
            ->for($patient)
            ->create([
                'start_time' => '2026-07-01 09:00:00',
                'end_time'   => '2026-07-01 09:30:00',
            ]);

        Appointment::factory()
            ->for($patient)
            ->create([
                'start_time' => '2026-08-01 09:00:00',
                'end_time'   => '2026-08-01 09:30:00',
            ]);

        $response = $this->getJson(
            "/api/patients/{$patient->id}/appointments?from=2026-07-01&to=2026-07-31"
        );

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_orders_appointments_ascending(): void
    {
        $patient = Patient::factory()->create();

        Appointment::factory()
            ->for($patient)
            ->create([
                'start_time' => '2026-07-03 09:00:00',
            ]);

        Appointment::factory()
            ->for($patient)
            ->create([
                'start_time' => '2026-07-01 09:00:00',
            ]);

        $response = $this->getJson(
            "/api/patients/{$patient->id}/appointments?order=asc"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.0.start_time',
                CarbonImmutable::parse('2026-07-01 09:00:00')->toRfc3339String()
            );
    }

    public function test_orders_appointments_descending(): void
    {
        $patient = Patient::factory()->create();

        Appointment::factory()
            ->for($patient)
            ->create([
                'start_time' => '2026-07-01 09:00:00',
            ]);

        Appointment::factory()
            ->for($patient)
            ->create([
                'start_time' => '2026-07-03 09:00:00',
            ]);

        $response = $this->getJson(
            "/api/patients/{$patient->id}/appointments?order=desc"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.0.start_time',
                CarbonImmutable::parse('2026-07-03 09:00:00')->toRfc3339String()
            );
    }

    public function test_returns_empty_collection_when_patient_has_no_appointments(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->getJson(
            "/api/patients/{$patient->id}/appointments"
        );

        $response
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_validates_the_request(): void
    {
        $patient = Patient::factory()->create();

        $this->getJson(
            "/api/patients/{$patient->id}/appointments?doctor_id=abc&from=foo&to=bar&order=test"
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'doctor_id',
                'from',
                'to',
                'order',
            ]);
    }
}
