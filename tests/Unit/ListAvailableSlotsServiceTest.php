<?php

namespace Tests\Unit\Domain\Availability\Services;

use App\DataSource\Repositories\AvailabilityRepositoryInterface;
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
    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow(
            CarbonImmutable::parse('2026-07-01 09:15:00')
        );
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        Mockery::close();

        parent::tearDown();
    }

    public function test_generates_only_future_slots(): void
    {
        $doctor = new Doctor();
        $doctor->id = 1;

        $availability = new Availability();
        $availability->starts_at = '2026-07-01 09:00:00';
        $availability->ends_at = '2026-07-01 10:30:00';
        $availability->slot_duration = 30;
        $availability->setRelation('doctor', $doctor);

        $query = new ListAvailableSlotsQuery(
            doctorId: 1,
            from: null,
            to: null,
        );

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);

        $repository
            ->shouldReceive('listAvailabilities')
            ->once()
            ->with($query)
            ->andReturn(new Collection([$availability]));

        $sut = new ListAvailableSlotsService($repository);

        $slots = $sut->list($query);

        $this->assertCount(2, $slots);

        /** @var Slot $first */
        $first = $slots->first();

        $this->assertEquals(
            CarbonImmutable::parse('2026-07-01 09:30:00'),
            $first->startsAt
        );

        $this->assertEquals(
            CarbonImmutable::parse('2026-07-01 10:00:00'),
            $first->endsAt
        );

        /** @var Slot $second */
        $second = $slots->last();

        $this->assertEquals(
            CarbonImmutable::parse('2026-07-01 10:00:00'),
            $second->startsAt
        );

        $this->assertEquals(
            CarbonImmutable::parse('2026-07-01 10:30:00'),
            $second->endsAt
        );
    }

    public function test_returns_empty_collection_when_repository_returns_no_availabilities(): void
    {
        $query = new ListAvailableSlotsQuery(
            doctorId: 1,
            from: null,
            to: null,
        );

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);

        $repository
            ->shouldReceive('listAvailabilities')
            ->once()
            ->with($query)
            ->andReturn(new Collection());

        $sut = new ListAvailableSlotsService($repository);

        $slots = $sut->list($query);

        $this->assertTrue($slots->isEmpty());
    }

    public function test_returns_no_slots_when_all_slots_are_in_the_past(): void
    {
        $doctor = new Doctor();
        $doctor->id = 1;

        $availability = new Availability();
        $availability->starts_at = '2026-07-01 08:00:00';
        $availability->ends_at = '2026-07-01 09:00:00';
        $availability->slot_duration = 30;
        $availability->setRelation('doctor', $doctor);

        $query = new ListAvailableSlotsQuery(
            doctorId: 1,
            from: null,
            to: null,
        );

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);

        $repository
            ->shouldReceive('listAvailabilities')
            ->once()
            ->andReturn(new Collection([$availability]));

        $sut = new ListAvailableSlotsService($repository);

        $slots = $sut->list($query);

        $this->assertTrue($slots->isEmpty());
    }
}
