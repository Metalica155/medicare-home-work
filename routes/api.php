<?php

use App\Http\Actions\Appointments\GetAppointmentAction;
use App\Http\Actions\Availabilities\GetAvailabilitiesAction;
use App\Http\Actions\Availabilities\ListAvailableSlotsAction;
use App\Http\Actions\Availabilities\StoreAvailabilityAction;
use App\Http\Actions\Doctors\DeleteDoctorAction;
use App\Http\Actions\Doctors\GetDoctorAction;
use App\Http\Actions\Doctors\GetDoctorsAction;
use App\Http\Actions\Doctors\StoreDoctorAction;
use App\Http\Actions\Doctors\UpdateDoctorAction;
use App\Http\Actions\Patients\DeletePatientAction;
use App\Http\Actions\Patients\GetPatientAction;
use App\Http\Actions\Patients\GetPatientsAction;
use App\Http\Actions\Patients\StorePatientAction;
use App\Http\Actions\Patients\UpdatePatientAction;
use Illuminate\Support\Facades\Route;

// Doctor CRUD
Route::get('/doctors', GetDoctorsAction::class);
Route::get('/doctors/{doctor}', GetDoctorAction::class);

Route::post('/doctors', StoreDoctorAction::class);
Route::patch('/doctors/{doctor}', UpdateDoctorAction::class);
Route::delete('/doctors/{doctor}', DeleteDoctorAction::class);

// Patient CRUD
Route::get('/patients', GetPatientsAction::class);
Route::get('/patients/{patient}', GetPatientAction::class);

Route::post('/patients', StorePatientAction::class);
Route::patch('/patients/{patient}', UpdatePatientAction::class);
Route::delete('/patients/{patient}', DeletePatientAction::class);

// Availability
Route::get('/availabilities', GetAvailabilitiesAction::class);
Route::get('/availabilities/slots', ListAvailableSlotsAction::class);

Route::post('/doctors/{doctor}/availabilities', StoreAvailabilityAction::class);

// Appointments
Route::get('/appointments/{appointment}', GetAppointmentAction::class);
