<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class DoctorIdFilter
{
    function __invoke(Builder $query, int $doctorId): Builder
    {
        return $query->whereHas(
            'doctor',
            fn(Builder $query) =>  $query->where('id', $doctorId)
        );
    }
}
