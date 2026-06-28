<?php

namespace App\Filters;

class AvailabilityFilters extends Filters
{
    protected $filters = [
        'from'      => FromFilter::class,
        'to'        => ToFilter::class,
        'doctor_id' => DoctorIdFilter::class,
    ];
}
