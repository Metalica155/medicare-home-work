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

    /**
     * @var array<string, array{
     *     from: AppointmentStatus[],
     *     rules: array<class-string<Rule>>
     * }>
     */
    private const array TRANSITIONS = [
        AppointmentStatus::Confirmed->value => [
            'from'  => [AppointmentStatus::Pending],
            'rules' => [],
        ],
        AppointmentStatus::Completed->value => [
            'from'  => [AppointmentStatus::Confirmed],
            'rules' => [
                AfterAppointmentRule::class,
            ],
        ],
        AppointmentStatus::Cancelled->value => [
            'from'  => [AppointmentStatus::Pending, AppointmentStatus::Confirmed],
            'rules' => [Before24HoursRule::class],
        ],
    ];

    public function validate(Appointment $appointment, AppointmentStatus $newStatus): void
    {
        if (! array_key_exists($newStatus->value, static::TRANSITIONS)) {
            throw new InvalidAppointmentStatusTransition();
        }

        $transition = static::TRANSITIONS[$newStatus->value];

        if (!in_array($appointment->status, $transition['from'], true)) {
            throw new InvalidAppointmentStatusTransition();
        }

        foreach ($transition['rules'] as $rule) {
            $this->ruleResolver->resolve($rule)->validate($appointment);
        }
    }
}
