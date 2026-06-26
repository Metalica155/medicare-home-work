<?php

namespace App\Http\Actions\Doctors;

use App\Http\Actions\Action;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\Response;

class StoreDoctorAction extends Action
{
    public function __invoke(StoreDoctorRequest $request)
    {
        $doctor = Doctor::create($request->validated());

        return DoctorResource::make($doctor)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
