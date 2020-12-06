<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\VO\Schedule;
use App\Entity\Vacation;
use App\Entity\Worker;
use PHPUnit\Framework\TestCase;

class WorkerTest extends TestCase
{
    public function testAddWorkerNormal()
    {
        $worker = Worker::create(1, 'Vasya', $schedule = Schedule::add(10, 19, 13, 14));
        $worker->addVacation(Vacation::add(new \DateTimeImmutable("2020-12-05"), new \DateTimeImmutable("2020-12-20")));

        $this->assertEquals(1, $worker->getData()["id"]);
        $this->assertEquals($schedule->getData(), $worker->getData()["scheduleWorkDay"]);
    }
}