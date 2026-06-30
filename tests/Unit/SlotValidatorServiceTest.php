<?php

namespace Tests\Unit;

use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Availability\Contracts\SlotGeneratorServiceInterface;
use App\Domain\Availability\Services\SlotValidatorService;
use App\Domain\Availability\ValueObjects\Slot;
use App\Models\Availability;
use Carbon\CarbonImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

class SlotValidatorServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        Mockery::close();

        parent::tearDown();
    }

    public function test_returns_false_when_no_containing_availability_exists(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 09:00:00');

        $start = CarbonImmutable::parse('2026-07-02 08:00:00');
        $end = CarbonImmutable::parse('2026-07-02 08:30:00');

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);
        $generator = Mockery::mock(SlotGeneratorServiceInterface::class);

        $repository
            ->shouldReceive('findContainingAvailability')
            ->once()
            ->with(1, $start, $end)
            ->andReturnNull();

        $generator
            ->shouldNotReceive('generateSlots');

        $sut = new SlotValidatorService(
            $generator,
            $repository,
        );

        $this->assertFalse(
            $sut->validate(1, $start, $end)
        );
    }

    public function test_returns_true_when_requested_slot_exists(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 09:00:00');

        $start = CarbonImmutable::parse('2026-07-02 08:00:00');
        $end = CarbonImmutable::parse('2026-07-02 08:30:00');

        $availability = new Availability();

        $slot = new Slot(
            availabilityId: 1,
            doctorId: 1,
            duration: 30,
            startsAt: $start,
            endsAt: $end,
        );

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);
        $generator = Mockery::mock(SlotGeneratorServiceInterface::class);

        $repository
            ->shouldReceive('findContainingAvailability')
            ->once()
            ->with(1, $start, $end)
            ->andReturn($availability);

        $generator
            ->shouldReceive('generateSlots')
            ->once()
            ->withArgs(function (
                Availability $passedAvailability,
                CarbonImmutable $now,
            ) use ($availability) {
                return $passedAvailability === $availability
                    && $now->equalTo(
                        CarbonImmutable::parse('2026-07-01 09:00:00')
                    );
            })
            ->andReturn(collect([$slot]));

        $sut = new SlotValidatorService(
            $generator,
            $repository,
        );

        $this->assertTrue(
            $sut->validate(1, $start, $end)
        );
    }

    public function test_returns_false_when_requested_slot_does_not_exist(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 09:00:00');

        $start = CarbonImmutable::parse('2026-07-02 08:00:00');
        $end = CarbonImmutable::parse('2026-07-02 08:30:00');

        $availability = new Availability();

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);
        $generator = Mockery::mock(SlotGeneratorServiceInterface::class);

        $repository
            ->shouldReceive('findContainingAvailability')
            ->once()
            ->with(1, $start, $end)
            ->andReturn($availability);

        $generator
            ->shouldReceive('generateSlots')
            ->once()
            ->andReturn(collect(
                [
                    new Slot(
                        availabilityId: 1,
                        doctorId: 1,
                        duration: 30,
                        startsAt: CarbonImmutable::parse('2026-07-02 08:30:00'),
                        endsAt: CarbonImmutable::parse('2026-07-02 09:00:00'),
                    ),
                ]
            ));

        $sut = new SlotValidatorService(
            $generator,
            $repository,
        );

        $this->assertFalse(
            $sut->validate(1, $start, $end)
        );
    }
}
