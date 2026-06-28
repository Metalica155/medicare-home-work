<?php

namespace Database\Factories;

use App\Models\Availability;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Availability>
 */
class AvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()
            ->dateTimeBetween('today', '+30 days')
            ->setTime(fake()->numberBetween(8, 16), 0);

        $slotDuration = fake()->randomElement([
            30,
            45,
            60,
        ]);

        $duration = fake()->numberBetween(1, 8) * $slotDuration;

        return [
            'doctor_id'     => Doctor::factory(),
            'starts_at'     => $startsAt,
            'ends_at'       => (clone $startsAt)->modify("+{$duration} minutes"),
            'slot_duration' => $slotDuration,
        ];
    }
}
