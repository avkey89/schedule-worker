<?php

declare(strict_types=1);

namespace App\Repository\Statics;

use App\Entity\EventCompany;
use App\Repository\StorageInterface;

class EventCompanyRepository implements StorageInterface
{

    public function find(int $id): ?EventCompany
    {
        return null;
    }

    public function all(): ?array
    {
        return $this->fixtures();
    }

    private function fixtures(): ?array
    {
        $event1 = EventCompany::add('Corporate', new \DateTimeImmutable("2020-12-25 15:00"), new \DateTimeImmutable("2020-12-26 00:00"));
        $event2 = EventCompany::add('Birthday company', new \DateTimeImmutable("2020-10-10 12:00"), new \DateTimeImmutable("2020-10-10 20:00"));

        return [
            1 => $event1,
            2 => $event2,
        ];
    }
}