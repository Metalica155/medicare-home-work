<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/swagger.yml', function () {
    return response()->file(
        base_path('/docs/swagger.yml'),
        [
            'Content-Type' => 'application/yaml',
        ]
    );
});

Route::view('/docs', 'swagger');
