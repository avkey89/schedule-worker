<?php

declare(strict_types=1);

namespace App\Tests\Functional\Schedule;

use App\Application\Schedule\CommandHandler\ScheduleCommandHandler;
use App\Entity\EventCompany;
use App\Entity\VO\Schedule;
use App\Entity\Vacation;
use App\Entity\Worker;
use App\Repository\Statics\EventCompanyRepository;
use App\Repository\Statics\WorkerRepository;
use PHPUnit\Framework\TestCase;

class ScheduleTest extends TestCase
{
    public function testGetScheduleOne()
    {
        $workerRepository = $this->createMock(WorkerRepository::class);
        $workerRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn($this->getWorker1());

        $eventRepository = $this->createMock(EventCompanyRepository::class);
        $eventRepository
            ->expects($this->once())
            ->method('all')
            ->willReturn($this->getEventCompany1());


        $request = ["startDate"=>"2020-12-09", "endDate"=>"2020-12-15", "userId"=>1];
        $schedule = new ScheduleCommandHandler($workerRepository, $eventRepository);
        $data = $schedule->handler((int)$request["userId"], $request["startDate"], $request["endDate"]);
        $this->assertEquals(3, count($data));
        $this->assertEquals(2, count($data[0]["timeRanges"]));
        $this->assertEquals(1, count($data[1]["timeRanges"]));
        $this->assertEquals(2, count($data[2]["timeRanges"]));

        $timeRanges = [0=>['start'=>'10:00', 'end'=>'14:00']];
        $this->assertEquals($timeRanges, $data[1]["timeRanges"]);
    }

    private function getWorker1()
    {
        $worker = Worker::create(1, 'Vasya', $schedule = Schedule::add(10, 19, 14, 15));
        $worker->addVacation(Vacation::add(new \DateTimeImmutable("2020-12-10"), new \DateTimeImmutable("2020-12-12")));

        return $worker;
    }

    private function getEventCompany1()
    {
        return [EventCompany::add('Corporate', new \DateTimeImmutable("2020-12-14 15:00"), new \DateTimeImmutable("2020-12-15 00:00"))];
    }

    public function testGetScheduleTwo()
    {
        $workerRepository = $this->createMock(WorkerRepository::class);
        $workerRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn($this->getWorker2());

        $eventRepository = $this->createMock(EventCompanyRepository::class);
        $eventRepository
            ->expects($this->once())
            ->method('all')
            ->willReturn($this->getEventCompany2());


        $request = ["startDate"=>"2020-12-09", "endDate"=>"2020-12-15", "userId"=>1];
        $schedule = new ScheduleCommandHandler($workerRepository, $eventRepository);
        $data = $schedule->handler((int)$request["userId"], $request["startDate"], $request["endDate"]);

        $this->assertEquals(3, count($data));
        $this->assertEquals(2, count($data[0]["timeRanges"]));
        $this->assertEquals(3, count($data[1]["timeRanges"]));
        $this->assertEquals(2, count($data[2]["timeRanges"]));

        $timeRanges = [
            0 => ['start'=>'10:00', 'end'=>'14:00'],
            1 => ['start'=>'15:00', 'end'=>'16:00'],
            2 => ['start'=>'18:00', 'end'=>'19:00']
        ];
        $this->assertEquals($timeRanges, $data[1]["timeRanges"]);
    }

    private function getWorker2()
    {
        $worker = Worker::create(1, 'Vasya', $schedule = Schedule::add(10, 19, 14, 15));
        $worker->addVacation(Vacation::add(new \DateTimeImmutable("2020-12-10"), new \DateTimeImmutable("2020-12-12")));

        return $worker;
    }

    private function getEventCompany2()
    {
        return [EventCompany::add('Corporate', new \DateTimeImmutable("2020-12-14 16:00"), new \DateTimeImmutable("2020-12-14 18:00"))];
    }

    public function testGetScheduleException()
    {
        $request = ["startDate"=>"2020-12-09", "endDate"=>"2020-12-15", "userId"=>10];
        $schedule = new ScheduleCommandHandler(new WorkerRepository(), new EventCompanyRepository());

        $this->expectException(\DomainException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage("Worker not found");

        $schedule->handler((int)$request["userId"], $request["startDate"], $request["endDate"]);
    }
}