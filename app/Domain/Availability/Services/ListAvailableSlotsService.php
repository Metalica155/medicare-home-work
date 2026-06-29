<?php

namespace App\Domain\Availability\Services;

use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Availability\Contracts\ListAvailableSlotsServiceInterface;
use App\Domain\Availability\Queries\ListAvailableSlotsQuery;
use App\Domain\Availability\ValueObjects\Slot;
use App\Models\Availability;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ListAvailableSlotsService implements ListAvailableSlotsServiceInterface
{
    public function __construct(
        private AvailabilityRepositoryInterface $repository,
    ) {}

    /**
     * @return Collection<int, Slot>
     */
    public function list(ListAvailableSlotsQuery $query): Collection
    {
        return $this->repository
            ->listAvailabilities($query)
            ->flatMap(fn(Availability $availability) => $this->generateSlots($availability, CarbonImmutable::now()));
    }

    /**
     * @return Slot[]
     */
    private function generateSlots(
        Availability $availability,
        CarbonImmutable $now,
    ): array {
        $slots = [];

        $current = CarbonImmutable::parse($availability->starts_at);
        $end = CarbonImmutable::parse($availability->ends_at);

        while (
            ($slotEnd = $current->addMinutes($availability->slot_duration))->lessThanOrEqualTo($end)
        ) {
            if ($current->greaterThan($now)) {
                $slots[] = new Slot(
                    doctorId: $availability->doctor->id,
                    duration: $availability->slot_duration,
                    startsAt: $current,
                    endsAt: $slotEnd,
                );
            }

            $current = $slotEnd;
        }

        return $slots;
    }
}
