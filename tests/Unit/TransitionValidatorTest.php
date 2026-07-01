<?php

namespace Tests\Unit;

use App\AppointmentStatus;
use App\Domain\Appointment\Exceptions\InvalidAppointmentStatusTransition;
use App\Domain\Appointment\Services\Transition\AfterAppointmentRule;
use App\Domain\Appointment\Services\Transition\Before24HoursRule;
use App\Domain\Appointment\Services\Transition\Rule;
use App\Domain\Appointment\Services\Transition\RuleResolver;
use App\Domain\Appointment\Services\Transition\TransitionValidator;
use App\Models\Appointment;
use Mockery;
use PHPUnit\Framework\TestCase;

class TransitionValidatorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_allows_pending_to_confirmed_transition(): void
    {
        $appointment = new Appointment();
        $appointment->status = AppointmentStatus::Pending;

        $resolver = Mockery::mock(RuleResolver::class);

        $resolver
            ->shouldNotReceive('resolve');

        $sut = new TransitionValidator($resolver);

        $sut->validate(
            $appointment,
            AppointmentStatus::Confirmed,
        );

        $this->assertTrue(true);
    }

    public function test_executes_after_appointment_rule(): void
    {
        $appointment = new Appointment();
        $appointment->status = AppointmentStatus::Confirmed;

        $rule = Mockery::mock(Rule::class);

        $rule
            ->shouldReceive('validate')
            ->once()
            ->with($appointment);

        $resolver = Mockery::mock(RuleResolver::class);

        $resolver
            ->shouldReceive('resolve')
            ->once()
            ->with(AfterAppointmentRule::class)
            ->andReturn($rule);

        $sut = new TransitionValidator($resolver);

        $sut->validate(
            $appointment,
            AppointmentStatus::Completed,
        );

        $this->expectNotToPerformAssertions();
    }

    public function test_executes_before_24_hours_rule(): void
    {
        $appointment = new Appointment();
        $appointment->status = AppointmentStatus::Pending;

        $rule = Mockery::mock(Rule::class);

        $rule
            ->shouldReceive('validate')
            ->once()
            ->with($appointment);

        $resolver = Mockery::mock(RuleResolver::class);

        $resolver
            ->shouldReceive('resolve')
            ->once()
            ->with(Before24HoursRule::class)
            ->andReturn($rule);

        $sut = new TransitionValidator($resolver);

        $sut->validate(
            $appointment,
            AppointmentStatus::Cancelled,
        );

        $this->expectNotToPerformAssertions();
    }

    public function test_throws_when_transition_is_not_allowed(): void
    {
        $appointment = new Appointment();
        $appointment->status = AppointmentStatus::Pending;

        $resolver = Mockery::mock(RuleResolver::class);

        $resolver
            ->shouldNotReceive('resolve');

        $sut = new TransitionValidator($resolver);

        $this->expectException(
            InvalidAppointmentStatusTransition::class
        );

        $sut->validate(
            $appointment,
            AppointmentStatus::Completed,
        );
    }
}
