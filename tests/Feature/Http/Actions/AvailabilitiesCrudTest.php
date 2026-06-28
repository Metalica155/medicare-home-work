<?php

namespace Tests\Feature\Http\Actions;

use App\Models\Availability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilitiesCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_doctors(): void
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
}
