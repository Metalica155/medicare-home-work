<?php

namespace App\Http\Actions\Availabilities;

use App\Domain\Availability\Contracts\ListAvailableSlotsServiceInterface;
use App\Domain\Availability\Queries\ListAvailableSlotsQuery;
use App\Http\Actions\Action;
use App\Http\Requests\ListAvailableSlotsRequest;
use App\Http\Resources\SlotsResource;

class ListAvailableSlotsAction extends Action
{
    public function __construct(
        private ListAvailableSlotsServiceInterface $service,
    ) {}

    public function __invoke(ListAvailableSlotsRequest $request): SlotsResource
    {
        return new SlotsResource(
            $this->service->list(
                ListAvailableSlotsQuery::fromArray($request->validated())
            )->paginate()
        );
    }
}
