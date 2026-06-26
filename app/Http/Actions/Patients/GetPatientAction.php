<?php

namespace App\Http\Actions\Patients;

use App\Http\Actions\Action;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\Request;

class GetPatientAction extends Action
{
    public function __invoke(Request $request, Patient $patient): PatientResource
    {
        return new PatientResource($patient);
    }
}
