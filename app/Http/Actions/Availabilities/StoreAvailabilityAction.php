<?php

namespace App\Http\Actions\Availabilities;

use App\Domain\Availability\Commands\CreateAvailabilityCommand;
use App\Domain\Availability\Contracts\CreateAvailabilityServiceInterface;
use App\Http\Actions\Action;
use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Resources\AvailabilityResource;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StoreAvailabilityAction extends Action
{
    public function __construct(
        private CreateAvailabilityServiceInterface $createAvailabilityService
    ) {}

    public function __invoke(StoreAvailabilityRequest $request, Doctor $doctor): JsonResponse
    {
        $availability = $this->createAvailabilityService->create(
            $doctor,
            CreateAvailabilityCommand::fromArray($request->validated())
        );

        return AvailabilityResource::make($availability)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
