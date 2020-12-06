<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\VO\Schedule;
use PHPUnit\Framework\TestCase;

class ScheduleTest extends TestCase
{
    public function testAddScheduleNormal()
    {
        $schedule = Schedule::add(10, 19);

        $this->assertEquals(10, $schedule->getData()["start"]);
    }

    public function testAddScheduleNormalWithDinner()
    {
        $schedule = Schedule::add(10, 19, 14, 15);

        $this->assertEquals(10, $schedule->getData()["start"]);
        $this->assertEquals(14, $schedule->getData()["dinnerStart"]);
    }
}