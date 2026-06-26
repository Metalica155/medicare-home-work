<?php

namespace App\Http\Actions\Patients;

use App\Http\Actions\Action;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Illuminate\Http\Response;

class UpdatePatientAction extends Action
{
    public function __invoke(UpdatePatientRequest $request, Patient $patient): Response
    {
        $patient->updateOrFail($request->validated());

        return response()->noContent();
    }
}
