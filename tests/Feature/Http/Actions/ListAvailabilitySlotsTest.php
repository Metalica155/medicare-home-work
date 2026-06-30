<?php

namespace Tests\Feature\Http\Actions;

use App\Models\Availability;
use App\Models\Doctor;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListAvailabilitySlotsTest extends TestCase
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

    public function test_returns_available_slots_for_a_doctor(): void
    {
        $doctor = Doctor::factory()->create();

        Availability::factory()->create([
            'doctor_id'     => $doctor->id,
            'starts_at'     => '2026-07-01 09:00:00',
            'ends_at'       => '2026-07-01 10:30:00',
            'slot_duration' => 30,
        ]);

        $response = $this->getJson('/api/availabilities/slots?' . http_build_query([
            'doctor_id' => $doctor->id,
        ]));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment([
                'doctor_id' => $doctor->id,
                'starts_at' => '2026-07-01T09:00:00+00:00',
                'ends_at'   => '2026-07-01T09:30:00+00:00',
            ])
            ->assertJsonFragment([
                'doctor_id' => $doctor->id,
                'starts_at' => '2026-07-01T09:30:00+00:00',
                'ends_at'   => '2026-07-01T10:00:00+00:00',
            ])
            ->assertJsonFragment([
                'doctor_id' => $doctor->id,
                'starts_at' => '2026-07-01T10:00:00+00:00',
                'ends_at'   => '2026-07-01T10:30:00+00:00',
            ]);
    }

    public function test_returns_validation_error_when_only_from_is_provided(): void
    {
        $this->getJson('/api/availabilities/slots?' . http_build_query([
            'from' => '2026-07-01',
        ]))->assertUnprocessable();
    }

    public function test_returns_validation_error_when_only_to_is_provided(): void
    {
        $this->getJson('/api/availabilities/slots?' . http_build_query([
            'to' => '2026-07-01',
        ]))->assertUnprocessable();
    }

    public function test_returns_validation_error_when_no_filters_are_provided(): void
    {
        $this->getJson('/api/availabilities/slots')
            ->assertUnprocessable();
    }

    public function test_does_not_return_past_slots(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse('2026-07-01 09:15:00')
        );

        $doctor = Doctor::factory()->create();

        Availability::factory()->create([
            'doctor_id'     => $doctor->id,
            'starts_at'     => '2026-07-01 09:00:00',
            'ends_at'       => '2026-07-01 10:30:00',
            'slot_duration' => 30,
        ]);

        $response = $this->getJson('/api/availabilities/slots?' . http_build_query([
            'doctor_id' => $doctor->id,
        ]));

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.starts_at', '2026-07-01T09:30:00+00:00')
            ->assertJsonPath('data.1.starts_at', '2026-07-01T10:00:00+00:00');
    }
}
