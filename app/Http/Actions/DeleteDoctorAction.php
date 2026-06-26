<?php

namespace App\Http\Actions;

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
