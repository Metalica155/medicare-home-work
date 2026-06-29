<?php

namespace App\Domain\Availability\ValueObjects;

use Carbon\CarbonImmutable;

final readonly class Slot
{
    public function __construct(
        public int $doctorId,
        public int $duration,
        public CarbonImmutable $startsAt,
        public CarbonImmutable $endsAt,
    ) {}
}
