<?php

namespace Tests\Feature\Http\Actions;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_patients(): void
    {
        Patient::factory()->count(20)->create();

        $response = $this->getJson('/api/patients');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'phone_number',
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

    public function test_returns_a_single_patient(): void
    {
        $patient = Patient::factory()->create([
            'name'         => 'John Doe',
            'email'        => 'john@example.com',
            'phone_number' => '+367066666',
        ]);

        $response = $this->getJson("/api/patients/{$patient->id}");

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id'           => $patient->id,
                    'name'         => 'John Doe',
                    'email'        => 'john@example.com',
                    'phone_number' => '+367066666',
                ],
            ]);
    }

    public function test_returns_404_when_patient_does_not_exist(): void
    {
        $this->getJson('/api/patient/999')->assertNotFound();
    }

    public function test_store_a_patient(): void
    {
        $payload = [
            'name'         => 'John Doe',
            'email'        => 'john@example.com',
            'phone_number' => '+367066666',
        ];

        $response = $this->postJson('/api/patients', $payload);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'name'         => 'John Doe',
                    'email'        => 'john@example.com',
                    'phone_number' => '+367066666',
                ],
            ]);

        $this->assertDatabaseHas('patients', [
            'name'         => 'John Doe',
            'email'        => 'john@example.com',
            'phone_number' => '+367066666',
        ]);
    }

    public function test_updates_a_patient(): void
    {
        $patient = Patient::factory()->create([
            'name'         => 'John Doe',
            'email'        => 'john@example.com',
            'phone_number' => '+367066666',
        ]);

        $payload = [
            'name'         => 'Jane Doe',
            'email'        => 'jane@example.com',
            'phone_number' => '+367077777',
        ];

        $response = $this->patchJson("/api/patients/{$patient->id}", $payload);

        $response->assertNoContent();

        $this->assertDatabaseHas('patients', [
            'name'         => 'Jane Doe',
            'email'        => 'jane@example.com',
            'phone_number' => '+367077777',
        ]);
    }

    public function test_deletes_a_patient(): void
    {
        $patient = Patient::factory()->create([
            'name'         => 'John Doe',
            'email'        => 'john@example.com',
            'phone_number' => '+367066666',
        ]);

        $response = $this->deleteJson("/api/patients/{$patient->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('patients', [
            'name'         => 'John Doe',
            'email'        => 'john@example.com',
            'phone_number' => '+367066666',
        ]);
    }

    public function test_validates_create_request(): void
    {
        Patient::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->postJson('/api/patients', [
            'name'         => '',
            'email'        => 'existing@example.com',
            'phone_number' => 'Not a phone number',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'email',
                'phone_number',
            ]);
    }

    public function test_validates_update_request(): void
    {
        $patient = Patient::factory()->create();

        Patient::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->patchJson("/api/patients/{$patient->id}", [
            'email'        => 'existing@example.com',
            'phone_number' => 'Not a phone number',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email',
                'phone_number',
            ]);
    }
}
