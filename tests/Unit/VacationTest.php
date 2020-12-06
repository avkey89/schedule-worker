<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Vacation;
use PHPUnit\Framework\TestCase;

class VacationTest extends TestCase
{
    public function testVacationNormal()
    {
        $vacation = Vacation::add($start = new \DateTimeImmutable("2020-12-05"), $end = new \DateTimeImmutable("2020-12-20"));

        $this->assertEquals($start, $vacation->getStart());
        $this->assertEquals($end, $vacation->getEnd());
    }

    public function testVacationException()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("End time vocation cannot be less than start");

        Vacation::add($start = new \DateTimeImmutable("2020-12-05"), $end = new \DateTimeImmutable("2020-12-03"));
    }
}