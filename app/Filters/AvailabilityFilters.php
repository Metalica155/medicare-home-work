<?php

namespace App\Filters;

class AvailabilityFilters extends Filters
{
    protected $filters = [
        'from'      => [FromFilter::class, 'starts_at'],
        'to'        => [ToFilter::class, 'ends_at'],
        'doctor_id' => [DoctorIdFilter::class, 'id'],
    ];
}
