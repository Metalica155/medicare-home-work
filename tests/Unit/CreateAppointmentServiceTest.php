<?php

namespace Tests\Unit;

use App\DataSource\Repositories\AppointmentRepositoryInterface;
use App\Domain\Appointment\Commands\CreateAppointmentCommand;
use App\Domain\Appointment\Contracts\SlotValidatorServiceInterface;
use App\Domain\Appointment\Exceptions\AppointmentInPastException;
use App\Domain\Appointment\Exceptions\DoctorAlreadyHaveAppointmentException;
use App\Domain\Appointment\Exceptions\InvalidSlotException;
use App\Domain\Appointment\Exceptions\PatientAlreadyHaveAppointmentException;
use App\Domain\Appointment\Services\CreateAppointmentService;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\CarbonImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;

class CreateAppointmentServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        Mockery::close();

        parent::tearDown();
    }

    public function test_creates_an_appointment(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 08:00:00');

        $patient = new Patient();
        $appointment = new Appointment();

        $command = $this->makeCommand();

        $repository = Mockery::mock(AppointmentRepositoryInterface::class);
        $validator = Mockery::mock(SlotValidatorServiceInterface::class);

        $repository
            ->shouldReceive('doctorHasAppointment')
            ->once()
            ->andReturnFalse();

        $repository
            ->shouldReceive('patientHasAppointment')
            ->once()
            ->andReturnFalse();

        $validator
            ->shouldReceive('validate')
            ->once()
            ->andReturnTrue();

        $repository
            ->shouldReceive('create')
            ->once()
            ->with($patient, $command)
            ->andReturn($appointment);

        $sut = new CreateAppointmentService(
            $repository,
            $validator,
        );

        $this->assertSame(
            $appointment,
            $sut->create($patient, $command)
        );
    }

    public function test_throws_when_appointment_is_in_the_past(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 08:00:00');

        $patient = new Patient();

        $command = $this->makeCommand('2026-06-30 09:00:00', '2026-06-30 09:30:00');

        $repository = Mockery::mock(AppointmentRepositoryInterface::class);
        $validator = Mockery::mock(SlotValidatorServiceInterface::class);

        $repository->shouldNotReceive('doctorHasAppointment');
        $repository->shouldNotReceive('patientHasAppointment');
        $repository->shouldNotReceive('create');
        $validator->shouldNotReceive('validate');

        $sut = new CreateAppointmentService(
            $repository,
            $validator,
        );

        $this->expectException(AppointmentInPastException::class);

        $sut->create($patient, $command);
    }

    public function test_throws_when_doctor_is_not_available(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 08:00:00');

        $patient = new Patient();

        $command = $this->makeCommand();

        $repository = Mockery::mock(AppointmentRepositoryInterface::class);
        $validator = Mockery::mock(SlotValidatorServiceInterface::class);

        $repository
            ->shouldReceive('doctorHasAppointment')
            ->once()
            ->andReturnTrue();

        $repository->shouldNotReceive('patientHasAppointment');
        $repository->shouldNotReceive('create');
        $validator->shouldNotReceive('validate');

        $sut = new CreateAppointmentService(
            $repository,
            $validator,
        );

        $this->expectException(DoctorAlreadyHaveAppointmentException::class);

        $sut->create($patient, $command);
    }

    public function test_throws_when_patient_is_not_available(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 08:00:00');

        $patient = new Patient();

        $command = $this->makeCommand();

        $repository = Mockery::mock(AppointmentRepositoryInterface::class);
        $validator = Mockery::mock(SlotValidatorServiceInterface::class);

        $repository
            ->shouldReceive('doctorHasAppointment')
            ->once()
            ->andReturnFalse();

        $repository
            ->shouldReceive('patientHasAppointment')
            ->once()
            ->andReturnTrue();

        $validator->shouldNotReceive('validate');
        $repository->shouldNotReceive('create');

        $sut = new CreateAppointmentService(
            $repository,
            $validator,
        );

        $this->expectException(PatientAlreadyHaveAppointmentException::class);

        $sut->create($patient, $command);
    }

    public function test_throws_when_slot_is_invalid(): void
    {
        CarbonImmutable::setTestNow('2026-07-01 08:00:00');

        $patient = new Patient();

        $command = $this->makeCommand();

        $repository = Mockery::mock(AppointmentRepositoryInterface::class);
        $validator = Mockery::mock(SlotValidatorServiceInterface::class);

        $repository
            ->shouldReceive('doctorHasAppointment')
            ->once()
            ->andReturnFalse();

        $repository
            ->shouldReceive('patientHasAppointment')
            ->once()
            ->andReturnFalse();

        $validator
            ->shouldReceive('validate')
            ->once()
            ->andReturnFalse();

        $repository->shouldNotReceive('create');

        $sut = new CreateAppointmentService(
            $repository,
            $validator,
        );

        $this->expectException(InvalidSlotException::class);

        $sut->create($patient, $command);
    }

    private function makeCommand(
        string $start = '2026-07-02 09:00:00',
        string $end = '2026-07-02 09:30:00',
        int $doctorId = 1,
    ): CreateAppointmentCommand {
        return new CreateAppointmentCommand(
            doctorId: $doctorId,
            startTime: CarbonImmutable::parse($start),
            endTime: CarbonImmutable::parse($end),
        );
    }
}
