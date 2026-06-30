<?php

namespace App\Filters;

class AppointmentFilters extends Filters
{
    protected $filters = [
        'from'      => [
            FromFilter::class,
            'start_time',
        ],
        'to'        => [
            ToFilter::class,
            'end_time',
        ],
        'doctor_id' => [
            DoctorIdFilter::class,
            'id',
        ],
        'status'    => [
            StatusFilter::class,
            'status',
        ],
    ];
}
