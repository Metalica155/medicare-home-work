<?php

namespace App\Domain\Appointment\Services\Transition;

use App\AppointmentStatus;
use App\Domain\Appointment\Exceptions\InvalidAppointmentStatusTransition;
use App\Models\Appointment;

class TransitionValidator
{
    public function __construct(
        private readonly RuleResolver $ruleResolver,
    ) {}

    private const array TRANSITIONS = [
        AppointmentStatus::Pending->value => [
            AppointmentStatus::Confirmed->value => [],
            AppointmentStatus::Cancelled->value => [],
        ],

        AppointmentStatus::Confirmed->value => [
            AppointmentStatus::Completed->value => [
                AfterAppointmentRule::class,
            ],

            AppointmentStatus::Cancelled->value => [
                Before24HoursRule::class,
            ],
        ],
    ];

    public function validate(Appointment $appointment, AppointmentStatus $newStatus): void
    {
        $rules = self::TRANSITIONS[$appointment->status->value][$newStatus->value]
            ?? throw new InvalidAppointmentStatusTransition();

        foreach ($rules as $rule) {
            $this->ruleResolver->resolve($rule)->validate($appointment);
        }
    }
}
