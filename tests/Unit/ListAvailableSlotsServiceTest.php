<?php

namespace Tests\Unit;

use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Availability\Contracts\SlotAvailabilityFilterServiceInterface;
use App\Domain\Availability\Contracts\SlotGeneratorServiceInterface;
use App\Domain\Availability\Queries\ListAvailableSlotsQuery;
use App\Domain\Availability\Services\ListAvailableSlotsService;
use App\Domain\Availability\ValueObjects\Slot;
use App\Models\Availability;
use App\Models\Doctor;
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
        $filter = Mockery::mock(SlotAvailabilityFilterServiceInterface::class);

        $sut = new ListAvailableSlotsService(
            $repository,
            $generator,
            $filter,
        );

        $repository
            ->shouldReceive('listAvailabilities')
            ->once()
            ->with($query)
            ->andReturn(new Collection());

        $generator->shouldNotReceive('generateSlots');
        $filter->shouldNotReceive('filter');

        $sut = new ListAvailableSlotsService(
            $repository,
            $generator,
            $filter,
        );

        $result = $sut->list($query);

        $this->assertTrue($result->isEmpty());
    }

    public function test_returns_generated_slots_for_a_single_availability(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 09:15:00');

        $doctor = new Doctor();
        $doctor->setRelation('appointments', new Collection());

        $availability = new Availability();
        $availability->setRelation('doctor', $doctor);

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

        $slots = collect(
            [
                $slot1,
                $slot2
            ]
        );

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);
        $generator = Mockery::mock(SlotGeneratorServiceInterface::class);
        $filter = Mockery::mock(SlotAvailabilityFilterServiceInterface::class);

        $repository
            ->shouldReceive('listAvailabilities')
            ->once()
            ->with($query)
            ->andReturn(new Collection([$availability]));

        $generator
            ->shouldReceive('generateSlots')
            ->once()
            ->withArgs(function (
                Availability $passedAvailability,
                CarbonImmutable $now
            ) use ($availability) {
                return $passedAvailability === $availability
                    && $now->equalTo(CarbonImmutable::parse('2026-07-01 09:15:00'));
            })
            ->andReturn($slots);

        $filter
            ->shouldReceive('filter')
            ->once()
            ->with(
                $slots,
                $doctor->appointments,
            )
            ->andReturn($slots);

        $sut = new ListAvailableSlotsService(
            $repository,
            $generator,
            $filter,
        );

        $result = $sut->list($query);

        $this->assertCount(2, $result);
        $this->assertSame($slot1, $result->get(0));
        $this->assertSame($slot2, $result->get(1));
    }

    public function test_returns_filtered_slots_for_a_single_availability(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 09:15:00');

        $doctor = new Doctor();
        $doctor->setRelation('appointments', new Collection());

        $availability = new Availability();
        $availability->setRelation('doctor', $doctor);

        $query = new ListAvailableSlotsQuery(
            doctorId: 1,
        );

        $generatedSlots = collect(
            [
                new Slot(
                    doctorId: 1,
                    duration: 30,
                    startsAt: CarbonImmutable::parse('2026-07-01 09:30'),
                    endsAt: CarbonImmutable::parse('2026-07-01 10:00'),
                ),
                new Slot(
                    doctorId: 1,
                    duration: 30,
                    startsAt: CarbonImmutable::parse('2026-07-01 10:00'),
                    endsAt: CarbonImmutable::parse('2026-07-01 10:30'),
                ),
            ]
        );


        $filteredSlots = collect([
            $generatedSlots[1],
        ]);

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);
        $generator = Mockery::mock(SlotGeneratorServiceInterface::class);
        $filter = Mockery::mock(SlotAvailabilityFilterServiceInterface::class);

        $repository
            ->shouldReceive('listAvailabilities')
            ->once()
            ->andReturn(new Collection([$availability]));

        $generator
            ->shouldReceive('generateSlots')
            ->once()
            ->andReturn($generatedSlots);

        $filter
            ->shouldReceive('filter')
            ->once()
            ->with(
                Mockery::on(fn($slots) => count($slots) === 2),
                Mockery::any(),
            )
            ->andReturn($filteredSlots);

        $sut = new ListAvailableSlotsService(
            $repository,
            $generator,
            $filter,
        );

        $result = $sut->list($query);

        $this->assertCount(1, $result);
        $this->assertSame($generatedSlots[1], $result->first());
    }
}
