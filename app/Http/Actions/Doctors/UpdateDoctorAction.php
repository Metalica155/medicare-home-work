<?php

namespace App\Http\Actions\Doctors;

use App\Http\Actions\Action;
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
