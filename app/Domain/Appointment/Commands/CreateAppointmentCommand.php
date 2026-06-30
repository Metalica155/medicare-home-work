<?php

namespace App\Domain\Appointment\Commands;

use Carbon\CarbonImmutable;

final readonly class CreateAppointmentCommand
{
    public function __construct(
        //public int $patientId,
        public int $doctorId,
        public CarbonImmutable $startTime,
        public CarbonImmutable $endTime,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            doctorId: $data['doctor_id'],
            startTime: CarbonImmutable::parse($data['start_time']),
            endTime: CarbonImmutable::parse($data['end_time']),
        );
    }
}
