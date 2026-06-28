<?php

namespace App\Http\Actions\Availabilities;

use App\Filters\AvailabilityFilters;
use App\Http\Actions\Action;
use App\Http\Requests\GetAvailabilitiesRequest;
use App\Models\Availability;
use App\Order;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GetAvailabilitiesAction extends Action
{
    public function __invoke(GetAvailabilitiesRequest $request): ResourceCollection
    {
        $filters = new AvailabilityFilters();

        return Availability::filter($filters)
            ->orderBy('starts_at', $request->validated('order', Order::Desc->value))
            ->paginate()
            ->toResourceCollection();
    }
}
