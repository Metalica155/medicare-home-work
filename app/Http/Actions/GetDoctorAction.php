<?php

namespace App\Http\Actions;

use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\Request;

class GetDoctorAction extends Action
{
    public function __invoke(Request $request, int $id): DoctorResource
    {
        return new DoctorResource(Doctor::findOrFail($id));
    }
}