<?php

namespace App\Domain\Availability\Queries;

use Carbon\CarbonImmutable;

final readonly class ListAvailableSlotsQuery
{
    public function __construct(
        public ?int $doctorId = null,
        public ?CarbonImmutable $from = null,
        public ?CarbonImmutable $to = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            doctorId: isset($data['doctor_id']) ? (int) $data['doctor_id'] : null,
            from: isset($data['from']) ? CarbonImmutable::parse($data['from']) : null,
            to: isset($data['to']) ? CarbonImmutable::parse($data['to']) : null,
        );
    }
}
