<?php

namespace App\Http\Actions\Doctors;

use App\Http\Actions\Action;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeleteDoctorAction extends Action
{
    public function __invoke(Request $request, Doctor $doctor): Response
    {
        $doctor->delete();

        return response()->noContent();
    }
}
