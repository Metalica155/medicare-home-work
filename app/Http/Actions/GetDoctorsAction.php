<?php

namespace App\Http\Actions;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetDoctorsAction extends Action
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        return Doctor::paginate()->toResourceCollection();
    }
}
