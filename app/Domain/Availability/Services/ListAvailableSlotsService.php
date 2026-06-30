<?php

namespace App\Domain\Availability\Services;

use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Availability\Contracts\ListAvailableSlotsServiceInterface;
use App\Domain\Availability\Contracts\SlotGeneratorServiceInterface;
use App\Domain\Availability\Queries\ListAvailableSlotsQuery;
use App\Domain\Availability\ValueObjects\Slot;
use App\Models\Availability;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ListAvailableSlotsService implements ListAvailableSlotsServiceInterface
{
    public function __construct(
        private AvailabilityRepositoryInterface $repository,
        private SlotGeneratorServiceInterface $slotGenerator,
    ) {}

    /**
     * @return Collection<int, Slot>
     */
    public function list(ListAvailableSlotsQuery $query): Collection
    {
        $now = CarbonImmutable::now();

        return $this->repository
            ->listAvailabilities($query)
            ->flatMap(fn(Availability $availability) => $this->slotGenerator->generateSlots(
                $availability,
                $now,
            ));
    }
}
