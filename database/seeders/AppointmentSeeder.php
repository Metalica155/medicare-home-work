<?php

namespace Database\Seeders;

use App\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = Doctor::all();
        $patients = Patient::all();

        if ($doctors->isEmpty() || $patients->isEmpty()) {
            return;
        }

        Appointment::factory()
            ->count(30)
            ->state(function () use ($doctors, $patients) {
                return [
                    'doctor_id'  => $doctors->random()->id,
                    'patient_id' => $patients->random()->id,
                ];
            })
            ->create();

        Appointment::factory()
            ->count(20)
            ->confirmed()
            ->state(function () use ($doctors, $patients) {
                return [
                    'doctor_id'  => $doctors->random()->id,
                    'patient_id' => $patients->random()->id,
                ];
            })
            ->create();

        Appointment::factory()
            ->count(20)
            ->completed()
            ->state(function () use ($doctors, $patients) {
                return [
                    'doctor_id'  => $doctors->random()->id,
                    'patient_id' => $patients->random()->id,
                ];
            })
            ->create();

        Appointment::factory()
            ->count(10)
            ->cancelled()
            ->state(function () use ($doctors, $patients) {
                return [
                    'doctor_id'  => $doctors->random()->id,
                    'patient_id' => $patients->random()->id,
                ];
            })
            ->create();
    }
}
