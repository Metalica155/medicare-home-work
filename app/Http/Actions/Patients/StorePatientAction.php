<?php

namespace App\Http\Actions\Patients;

use App\Http\Actions\Action;
use App\Http\Requests\StorePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\Response;

class StorePatientAction extends Action
{
    public function __invoke(StorePatientRequest $request)
    {
        $patient = Patient::create($request->validated());

        return PatientResource::make($patient)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
