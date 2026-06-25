<?php

use App\Http\Actions\CreateDoctorAction;
use App\Http\Actions\GetDoctorAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function (Request $request) {
    return 'pong';
});

Route::post('doctors', CreateDoctorAction::class);
Route::get('/doctors/{id}', GetDoctorAction::class);
