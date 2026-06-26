<?php

namespace App\Http\Actions;

use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Doctor;
use Illuminate\Http\Response;

class UpdateDoctorAction extends Action
{
    public function __invoke(UpdateDoctorRequest $request, Doctor $doctor): Response
    {
        $doctor->updateOrFail($request->validated());

        return response()->noContent();
    }
}
