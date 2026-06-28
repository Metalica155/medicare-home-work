<?php

namespace Tests\Unit\Domain\Availability\Services;

use App\DataSource\Repositories\AvailabilityRepositoryInterface;
use App\Domain\Availability\Commands\CreateAvailabilityCommand;
use App\Domain\Availability\Exceptions\AvailabilityInPastException;
use App\Domain\Availability\Exceptions\AvailabilityOverlapException;
use App\Domain\Availability\Services\CreateAvailabilityService;
use App\Models\Availability;
use App\Models\Doctor;
use Carbon\CarbonImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

class CreateAvailabilityServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_creates_an_availability(): void
    {
        $doctor = new Doctor();
        $availability = new Availability();

        $command = new CreateAvailabilityCommand(
            startsAt: CarbonImmutable::now()->addDay(),
            endsAt: CarbonImmutable::now()->addDay()->addHour(),
            slotDuration: 30,
        );

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);

        $repository
            ->shouldReceive('overlaps')
            ->once()
            ->with($doctor, $command->startsAt, $command->endsAt)
            ->andReturn(false);

        $repository
            ->shouldReceive('create')
            ->once()
            ->with($doctor, $command)
            ->andReturn($availability);

        $sut = new CreateAvailabilityService($repository);

        $result = $sut->create($doctor, $command);

        $this->assertSame($availability, $result);
    }

    public function test_throws_when_availability_starts_in_the_past(): void
    {
        $this->expectException(AvailabilityInPastException::class);

        $doctor = new Doctor();

        $command = new CreateAvailabilityCommand(
            startsAt: CarbonImmutable::now()->subMinute(),
            endsAt: CarbonImmutable::now()->addHour(),
            slotDuration: 30,
        );

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);

        $repository->shouldNotReceive('overlaps');
        $repository->shouldNotReceive('create');

        $sut = new CreateAvailabilityService($repository);

        $sut->create($doctor, $command);
    }

    public function test_throws_when_availability_overlaps(): void
    {
        $this->expectException(AvailabilityOverlapException::class);

        $doctor = new Doctor();

        $command = new CreateAvailabilityCommand(
            startsAt: CarbonImmutable::now()->addDay(),
            endsAt: CarbonImmutable::now()->addDay()->addHour(),
            slotDuration: 30,
        );

        $repository = Mockery::mock(AvailabilityRepositoryInterface::class);

        $repository
            ->shouldReceive('overlaps')
            ->once()
            ->andReturn(true);

        $repository->shouldNotReceive('create');

        $sut = new CreateAvailabilityService($repository);

        $sut->create($doctor, $command);
    }
}
