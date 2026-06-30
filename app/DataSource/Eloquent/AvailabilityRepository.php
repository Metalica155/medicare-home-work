<?php

namespace App\DataSource\Eloquent;

use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Availability\Commands\CreateAvailabilityCommand;
use App\Domain\Availability\Queries\ListAvailableSlotsQuery;
use App\Models\Availability;
use App\Models\Doctor;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class AvailabilityRepository implements AvailabilityRepositoryInterface
{
    public function create(
        Doctor $doctor,
        CreateAvailabilityCommand $command,
    ): Availability {
        return $doctor->availabilities()->create([
            'starts_at'     => $command->startsAt,
            'ends_at'       => $command->endsAt,
            'slot_duration' => $command->slotDuration,
        ]);
    }

    public function overlaps(Doctor $doctor, CarbonImmutable $startsAt, CarbonImmutable $endsAt): bool
    {
        return $doctor->availabilities()
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();
    }

    /**
     * @return Collection<int, Availability>
     */
    public function listAvailabilities(
        ListAvailableSlotsQuery $query,
    ): Collection {
        return Availability::query()
            ->with('doctor')
            ->when(
                $query->doctorId,
                fn($builder) => $builder->where('doctor_id', $query->doctorId),
            )
            ->when(
                $query->from && $query->to,
                fn($builder) => $builder
                    ->where('starts_at', '<', $query->to)
                    ->where('ends_at', '>', $query->from),
                fn($builder) => $builder
                    ->where('ends_at', '>', CarbonImmutable::now()),
            )
            ->orderBy('starts_at')
            ->get();
    }
}
