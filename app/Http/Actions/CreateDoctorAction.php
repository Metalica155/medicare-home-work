<?php

namespace App\Http\Actions;

use App\Http\Requests\CreateDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\Response;

class CreateDoctorAction extends Action
{
    public function __invoke(CreateDoctorRequest $request)
    {
        $doctor = Doctor::create($request->validated());

        return DoctorResource::make($doctor)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
