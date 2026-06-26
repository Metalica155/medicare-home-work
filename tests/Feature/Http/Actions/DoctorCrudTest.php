<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DoctorCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_doctors(): void
    {
        Doctor::factory()->count(20)->create();

        $response = $this->getJson('/api/doctors');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'expertise',
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

    public function test_returns_a_single_doctor(): void
    {
        $doctor = Doctor::factory()->create([
            'name'      => 'John Doe',
            'email'     => 'john@example.com',
            'expertise' => 'Cardiologist',
        ]);

        $response = $this->getJson("/api/doctors/{$doctor->id}");

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id'        => $doctor->id,
                    'name'      => 'John Doe',
                    'email'     => 'john@example.com',
                    'expertise' => 'Cardiologist',
                ],
            ]);
    }

    public function test_returns_404_when_doctor_does_not_exist(): void
    {
        $this->getJson('/api/doctors/999')->assertNotFound();
    }

    public function test_creates_a_doctor(): void
    {
        $payload = [
            'name'      => 'John Doe',
            'email'     => 'john@example.com',
            'expertise' => 'Cardiologist',
        ];

        $response = $this->postJson('/api/doctors', $payload);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'name'      => 'John Doe',
                    'email'     => 'john@example.com',
                    'expertise' => 'Cardiologist',
                ],
            ]);

        $this->assertDatabaseHas('doctors', [
            'name'      => 'John Doe',
            'email'     => 'john@example.com',
            'expertise' => 'Cardiologist',
        ]);
    }

    public function test_updates_a_doctor(): void
    {
        $doctor = Doctor::factory()->create([
            'name'      => 'John Doe',
            'email'     => 'john@example.com',
            'expertise' => 'Cardiologist',
        ]);

        $payload = [
            'name'      => 'Jane Doe',
            'email'     => 'jane@example.com',
            'expertise' => 'Dermatologist',
        ];

        $response = $this->patchJson("/api/doctors/{$doctor->id}", $payload);

        $response->assertNoContent();

        $this->assertDatabaseHas('doctors', [
            'name'      => 'Jane Doe',
            'email'     => 'jane@example.com',
            'expertise' => 'Dermatologist',
        ]);
    }

    public function test_deletes_a_doctor(): void
    {
        $doctor = Doctor::factory()->create([
            'name'      => 'John Doe',
            'email'     => 'john@example.com',
            'expertise' => 'Cardiologist',
        ]);

        $response = $this->deleteJson("/api/doctors/{$doctor->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('doctors', [
            'name'      => 'John Doe',
            'email'     => 'john@example.com',
            'expertise' => 'Cardiologist',
        ]);
    }

    public function test_validates_create_request(): void
    {
        Doctor::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->postJson('/api/doctors', [
            'name'      => '',
            'email'     => 'existing@example.com',
            'expertise' => 'Astronaut',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'email',
                'expertise',
            ]);
    }

    public function test_validates_update_request(): void
    {
        $doctor = Doctor::factory()->create();

        Doctor::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->patchJson("/api/doctors/{$doctor->id}", [
            'email'     => 'existing@example.com',
            'expertise' => 'Astronaut',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
                'expertise',
            ]);
    }
}
