<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        Doctor::factory()->count(10)->create();
    }
}
