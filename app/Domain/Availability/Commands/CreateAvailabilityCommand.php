<?php

namespace App\Domain\Availability\Commands;

use Carbon\CarbonImmutable;

final readonly class CreateAvailabilityCommand
{
    public function __construct(
        public CarbonImmutable $startsAt,
        public CarbonImmutable $endsAt,
        public int $slotDuration,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            startsAt: CarbonImmutable::parse($data['starts_at']),
            endsAt: CarbonImmutable::parse($data['ends_at']),
            slotDuration: $data['slot_duration'],
        );
    }
}
