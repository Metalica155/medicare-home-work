<?php

use App\Http\Actions\CreateDoctorAction;
use App\Http\Actions\DeleteDoctorAction;
use App\Http\Actions\GetDoctorAction;
use App\Http\Actions\GetDoctorsAction;
use App\Http\Actions\UpdateDoctorAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function (Request $request) {
    return 'pong';
});

// Doctor CRUD
Route::get('/doctors', GetDoctorsAction::class);
Route::get('/doctors/{doctor}', GetDoctorAction::class);

Route::post('/doctors', CreateDoctorAction::class);
Route::patch('/doctors/{doctor}', UpdateDoctorAction::class);
Route::delete('/doctors/{doctor}', DeleteDoctorAction::class);
