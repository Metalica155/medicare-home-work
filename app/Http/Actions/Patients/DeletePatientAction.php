<?php

namespace App\Http\Actions\Patients;

use App\Http\Actions\Action;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeletePatientAction extends Action
{
    public function __invoke(Request $request, Patient $patient): Response
    {
        $patient->delete();

        return response()->noContent();
    }
}
