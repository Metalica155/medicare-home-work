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
        ListAvailableSlotsQuery $input,
    ): Collection {
        return Availability::query()
            ->with([
                'doctor',
                'doctor.appointments' => function ($query) use ($input) {
                    $query->when(
                        $input->from && $input->to,
                        fn($builder) => $builder
                            ->where('start_time', '<=', $input->to)
                            ->where('end_time', '>=', $input->from),
                        fn($builder) => $builder
                            ->where('end_time', '>=', CarbonImmutable::now()),
                    );
                },
            ])
            ->when(
                $input->doctorId,
                fn($builder) => $builder->where('doctor_id', $input->doctorId),
            )
            ->when(
                $input->from && $input->to,
                fn($builder) => $builder
                    ->where('starts_at', '<', $input->to)
                    ->where('ends_at', '>', $input->from),
                fn($builder) => $builder
                    ->where('ends_at', '>', CarbonImmutable::now()),
            )
            ->orderBy('starts_at')
            ->get();
    }

    public function findContainingAvailability(
        int $doctorId,
        CarbonImmutable $startsAt,
        CarbonImmutable $endsAt,
    ): ?Availability {
        return Availability::query()
            ->where('doctor_id', $doctorId)
            ->where('starts_at', '<=', $startsAt)
            ->where('ends_at', '>=', $endsAt)
            ->orderBy('starts_at')
            ->first();
    }
}
