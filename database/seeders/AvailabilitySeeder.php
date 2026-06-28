<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class AvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = Doctor::all();

        foreach ($doctors as $doctor) {
            foreach ($this->generateAvailabilities() as $availability) {

                Availability::factory()
                    ->for($doctor)
                    ->create($availability);
            }
        }
    }

    private function generateAvailabilities(): Collection
    {
        return collect(range(0, 6))->map(function ($offset) {
            $date = today()->addDays($offset);
            $slotDuration = fake()->randomElement([
                30,
                45,
                60,
            ]);

            $endAtMinutes = fake()->numberBetween(4, 8) * $slotDuration;

            return [
                'starts_at'     => $date->copy()->setTime(8, 0),
                'ends_at'       => $date->copy()->setTime(8, $endAtMinutes),
                'slot_duration' => $slotDuration,
            ];
        });
    }
}
