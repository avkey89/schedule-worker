<?php

declare(strict_types=1);

namespace App\Repository\Statics;

use App\Entity\VO\Schedule;
use App\Entity\Vacation;
use App\Entity\Worker;
use App\Repository\StorageInterface;

class WorkerRepository implements StorageInterface
{

    public function find(int $id): ?Worker
    {
        if (!$worker = $this->fixtures($id)) {
            throw new \DomainException("Worker not found", 400);
        }

        return $worker;
    }

    public function all(): ?array
    {
        // TODO: Implement all() method.
    }

    private function fixtures(int $id): ?Worker
    {
        $worker1 = Worker::create(1, 'Vasya', $schedule = Schedule::add(10, 19, 13, 14));
        $worker1->addVacation(Vacation::add(new \DateTimeImmutable("2020-12-05"), new \DateTimeImmutable("2020-12-20")));

        $worker2 = Worker::create(1, 'Vasya', $schedule = Schedule::add(10, 19, 13, 14));
        $worker2->addVacation(Vacation::add(new \DateTimeImmutable("2020-12-05"), new \DateTimeImmutable("2020-12-20")));

        $workers = [
            1 => $worker1,
            2 => $worker2,
        ];

        return $workers[$id] ?? null;
    }

}