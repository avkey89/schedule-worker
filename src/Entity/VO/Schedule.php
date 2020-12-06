<?php

declare(strict_types=1);

namespace App\Entity\VO;

/**
 * @ORM\Embeddable
 */
class Schedule
{
    private const MIN_TIME_DAY = 0;
    private const MAX_TIME_DAY = 24;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $start;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $end;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $dinnerStart;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $dinnerEnd;

    public function __construct(int $start, int $end, ?int $dinnerStart = null, ?int $dinnerEnd = null)
    {
        if ($start < self::MIN_TIME_DAY || $end > self::MAX_TIME_DAY) {
            throw new \DomainException("Work time go beyond");
        }
        if ($dinnerStart > $dinnerEnd) {
            throw new \DomainException("Incorrect dinner time");
        }
        if ($dinnerStart && $dinnerStart <= $start) {
            throw new \DomainException("Dinner start cannot less or equal than start work time");
        }
        if ($dinnerEnd && $dinnerEnd >= $end) {
            throw new \DomainException("Dinner end cannot more or equal than end work time");
        }
        $this->start = $start;
        $this->end = $end;
        $this->dinnerStart = $dinnerStart;
        $this->dinnerEnd = $dinnerEnd;
    }

    public static function add(int $start, int $end, ?int $dinnerStart = null, ?int $dinnerEnd = null): self
    {
        return new self($start, $end, $dinnerStart, $dinnerEnd);
    }

    public function getData(): array
    {
        return [
            "start" => $this->start,
            "end" => $this->end,
            "dinnerStart" => $this->dinnerStart,
            "dinnerEnd" => $this->dinnerEnd
        ];
    }
}