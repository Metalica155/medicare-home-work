<?php

namespace App\Http\Actions\Patients;

use App\Http\Actions\Action;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetPatientsAction extends Action
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        return Patient::paginate()->toResourceCollection();
    }
}
