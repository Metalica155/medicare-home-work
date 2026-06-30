<?php

namespace Tests\Unit;

use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Availability\Contracts\SlotGeneratorServiceInterface;
use App\Domain\Availability\Queries\ListAvailableSlotsQuery;
use App\Domain\Availability\Services\ListAvailableSlotsService;
use App\Domain\Availability\ValueObjects\Slot;
use App\Models\Availability;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class ListAvailableSlotsServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        Mockery::close();

        parent::tearDown();
    }

    public function test_returns_empty_collection_when_repository_returns_no_availabilities(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 09:15:00');

        $query = new ListAvailableSlotsQuery(
            doctorId: 1,
        );

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);
        $generator = Mockery::mock(SlotGeneratorServiceInterface::class);

        $repository
            ->shouldReceive('listAvailabilities')
            ->once()
            ->with($query)
            ->andReturn(new Collection());

        $generator
            ->shouldNotReceive('generateSlots');

        $sut = new ListAvailableSlotsService(
            $repository,
            $generator,
        );

        $result = $sut->list($query);

        $this->assertTrue($result->isEmpty());
    }

    public function test_returns_generated_slots_for_a_single_availability(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 09:15:00');

        $availability = new Availability();

        $query = new ListAvailableSlotsQuery(
            doctorId: 1,
        );

        $slot1 = new Slot(
            doctorId: 1,
            duration: 30,
            startsAt: CarbonImmutable::parse('2026-07-01 09:30:00'),
            endsAt: CarbonImmutable::parse('2026-07-01 10:00:00'),
        );

        $slot2 = new Slot(
            doctorId: 1,
            duration: 30,
            startsAt: CarbonImmutable::parse('2026-07-01 10:00:00'),
            endsAt: CarbonImmutable::parse('2026-07-01 10:30:00'),
        );

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);
        $generator = Mockery::mock(SlotGeneratorServiceInterface::class);

        $repository
            ->shouldReceive('listAvailabilities')
            ->once()
            ->with($query)
            ->andReturn(new Collection([$availability]));

        $generator
            ->shouldReceive('generateSlots')
            ->once()
            ->withArgs(function (Availability $passedAvailability, CarbonImmutable $now) use ($availability) {
                return $passedAvailability === $availability
                    && $now->equalTo(CarbonImmutable::parse('2026-07-01 09:15:00'));
            })
            ->andReturn([$slot1, $slot2]);

        $sut = new ListAvailableSlotsService(
            $repository,
            $generator,
        );

        $result = $sut->list($query);

        $this->assertCount(2, $result);
        $this->assertSame($slot1, $result->get(0));
        $this->assertSame($slot2, $result->get(1));
    }

    public function test_flattens_slots_from_multiple_availabilities(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 09:15:00');

        $availability1 = new Availability();
        $availability2 = new Availability();

        $query = new ListAvailableSlotsQuery(
            doctorId: 1,
        );

        $slot1 = new Slot(
            doctorId: 1,
            duration: 30,
            startsAt: CarbonImmutable::parse('2026-07-01 09:30:00'),
            endsAt: CarbonImmutable::parse('2026-07-01 10:00:00'),
        );

        $slot2 = new Slot(
            doctorId: 1,
            duration: 30,
            startsAt: CarbonImmutable::parse('2026-07-01 10:00:00'),
            endsAt: CarbonImmutable::parse('2026-07-01 10:30:00'),
        );

        $slot3 = new Slot(
            doctorId: 1,
            duration: 30,
            startsAt: CarbonImmutable::parse('2026-07-01 10:30:00'),
            endsAt: CarbonImmutable::parse('2026-07-01 11:00:00'),
        );

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);
        $generator = Mockery::mock(SlotGeneratorServiceInterface::class);

        $repository
            ->shouldReceive('listAvailabilities')
            ->once()
            ->with($query)
            ->andReturn(new Collection([
                $availability1,
                $availability2,
            ]));

        $generator
            ->shouldReceive('generateSlots')
            ->once()
            ->with($availability1, Mockery::type(CarbonImmutable::class))
            ->andReturn([$slot1]);

        $generator
            ->shouldReceive('generateSlots')
            ->once()
            ->with($availability2, Mockery::type(CarbonImmutable::class))
            ->andReturn([$slot2, $slot3]);

        $sut = new ListAvailableSlotsService(
            $repository,
            $generator,
        );

        $result = $sut->list($query);

        $this->assertCount(3, $result);

        $this->assertSame($slot1, $result->get(0));
        $this->assertSame($slot2, $result->get(1));
        $this->assertSame($slot3, $result->get(2));
    }
}
