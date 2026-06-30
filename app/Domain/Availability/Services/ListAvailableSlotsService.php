<?php

namespace App\Domain\Availability\Services;

use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Availability\Contracts\ListAvailableSlotsServiceInterface;
use App\Domain\Availability\Contracts\SlotAvailabilityFilterServiceInterface;
use App\Domain\Availability\Contracts\SlotGeneratorServiceInterface;
use App\Domain\Availability\Queries\ListAvailableSlotsQuery;
use App\Domain\Availability\ValueObjects\Slot;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ListAvailableSlotsService implements ListAvailableSlotsServiceInterface
{
    public function __construct(
        private AvailabilityRepositoryInterface $repository,
        private SlotGeneratorServiceInterface $slotGenerator,
        private SlotAvailabilityFilterServiceInterface $slotFilterService,
    ) {}

    /**
     * @return Collection<int, Slot>
     */
    public function list(ListAvailableSlotsQuery $query): Collection
    {
        $now = CarbonImmutable::now();

        $slots = new Collection();

        foreach ($this->repository->listAvailabilities($query) as $availability) {
            $slots = $slots->merge(
                $this->slotFilterService->filter(
                    $this->slotGenerator->generateSlots($availability, $now),
                    $availability->doctor->appointments,
                )
            );
        }

        return $slots;
    }
}
