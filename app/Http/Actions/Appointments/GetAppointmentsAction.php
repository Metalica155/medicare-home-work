<?php

namespace App\Http\Actions\Appointments;

use App\Filters\AppointmentFilters;
use App\Http\Requests\GetAppointmentsRequest;
use App\Models\Patient;
use App\Order;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GetAppointmentsAction
{
    public function __invoke(Patient $patient, GetAppointmentsRequest $request): ResourceCollection
    {
        $filters = new AppointmentFilters();

        return $patient->appointments()->filter($filters)
            ->orderBy('start_time', $request->validated('order', Order::Desc->value))
            ->paginate()
            ->toResourceCollection();
    }
}
