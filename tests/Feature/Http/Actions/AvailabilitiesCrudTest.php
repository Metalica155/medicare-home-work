<?php

namespace Tests\Feature\Http\Actions;

use App\Models\Availability;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilitiesCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-06-28 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_list_availabilities(): void
    {
        Availability::factory()->count(20)->create();

        $response = $this->getJson('/api/availabilities');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'starts_at',
                        'ends_at',
                        'slot_duration',
                        'doctor',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ])
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('meta.total', 20)
            ->assertJsonPath('meta.per_page', 15)
            ->assertJsonPath('meta.current_page', 1);
    }

    public function test_store_availability(): void
    {
        $doctor = Doctor::factory()->create();

        $startsAt = now()->addDay()->setTime(9, 0);
        $endsAt = $startsAt->copy()->addDay()->setTime(10, 0);
        $slotDuration = 30;

        $payload = [
            'starts_at'     => $startsAt->toDateTimeString(),
            'ends_at'       => $endsAt->toDateTimeString(),
            'slot_duration' => $slotDuration,
        ];

        $response = $this->postJson(
            "/api/doctors/{$doctor->id}/availabilities",
            $payload
        );

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'starts_at',
                    'ends_at',
                    'slot_duration',
                    'doctor',
                ],
            ]);

        $this->assertDatabaseHas('availabilities', [
            'doctor_id' => $doctor->id,
            'starts_at' => $startsAt,
            'ends_at'   => $endsAt,
        ]);
    }
}
