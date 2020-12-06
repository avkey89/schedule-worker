<?php

declare(strict_types=1);

namespace App\Entity;

class EventCompany
{
    private string $event;
    private \DateTimeImmutable $start;
    private \DateTimeImmutable $end;

    public function __construct(string $event, \DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        $this->event = $event;
        $this->start = $start;
        $this->end = $end;
    }

    public static function add(string $event, \DateTimeImmutable $start, \DateTimeImmutable $end): self
    {
        return new self($event, $start, $end);
    }

    public function getStart(): \DateTimeImmutable
    {
        return $this->start;
    }

    public function getEnd(): \DateTimeImmutable
    {
        return $this->end;
    }
}