<?php

namespace Tests\Unit;

use App\Domain\Availability\Services\SlotGeneratorService;
use App\Domain\Availability\ValueObjects\Slot;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Doctor;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class SlotGeneratorServiceTest extends TestCase
{
    private SlotGeneratorService $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new SlotGeneratorService();
    }

    public function test_generates_all_future_slots(): void
    {
        $doctor = new Doctor();
        $doctor->id = 1;

        $availability = new Availability();
        $availability->id = 1;
        $availability->starts_at = '2026-07-01 09:00:00';
        $availability->ends_at = '2026-07-01 10:30:00';
        $availability->slot_duration = 30;
        $availability->setRelation('doctor', $doctor);

        $slots = $this->sut->generateSlots(
            $availability,
            CarbonImmutable::parse('2026-07-01 08:00:00'),
        );

        $this->assertCount(3, $slots);

        /** @var Slot $first */
        $first = $slots[0];

        $this->assertEquals(
            CarbonImmutable::parse('2026-07-01 09:00:00'),
            $first->startsAt
        );

        $this->assertEquals(
            CarbonImmutable::parse('2026-07-01 09:30:00'),
            $first->endsAt
        );
    }

    public function test_filters_out_past_slots(): void
    {
        $doctor = new Doctor();
        $doctor->id = 1;

        $availability = new Availability();
        $availability->id = 1;
        $availability->starts_at = '2026-07-01 09:00:00';
        $availability->ends_at = '2026-07-01 10:30:00';
        $availability->slot_duration = 30;
        $availability->setRelation('doctor', $doctor);

        $slots = $this->sut->generateSlots(
            $availability,
            CarbonImmutable::parse('2026-07-01 09:15:00'),
        );

        $this->assertCount(2, $slots);

        $this->assertEquals(
            CarbonImmutable::parse('2026-07-01 09:30:00'),
            $slots[0]->startsAt
        );

        $this->assertEquals(
            CarbonImmutable::parse('2026-07-01 10:00:00'),
            $slots[1]->startsAt
        );
    }

    public function test_returns_no_slots_when_everything_is_in_the_past(): void
    {
        $doctor = new Doctor();
        $doctor->id = 1;

        $availability = new Availability();
        $availability->id = 1;
        $availability->starts_at = '2026-07-01 08:00:00';
        $availability->ends_at = '2026-07-01 09:00:00';
        $availability->slot_duration = 30;
        $availability->setRelation('doctor', $doctor);

        $slots = $this->sut->generateSlots(
            $availability,
            CarbonImmutable::parse('2026-07-01 09:15:00'),
        );

        $this->assertEmpty($slots);
    }

    public function test_does_not_generate_partial_slots(): void
    {
        $doctor = new Doctor();
        $doctor->id = 1;

        $availability = new Availability();
        $availability->id = 1;
        $availability->id = 1;
        $availability->starts_at = '2026-07-01 09:00:00';
        $availability->ends_at = '2026-07-01 10:20:00';
        $availability->slot_duration = 30;
        $availability->setRelation('doctor', $doctor);

        $slots = $this->sut->generateSlots(
            $availability,
            CarbonImmutable::parse('2026-07-01 08:00:00'),
        );

        $this->assertCount(2, $slots);

        $this->assertEquals(
            CarbonImmutable::parse('2026-07-01 10:00:00'),
            $slots[1]->endsAt
        );
    }
}
