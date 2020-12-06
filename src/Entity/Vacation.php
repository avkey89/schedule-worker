<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * @ORM\Entity()
 */
class Vacation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private \DateTimeImmutable $start;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private \DateTimeImmutable $end;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Worker", inversedBy="vacation")
     */
    private Worker $worker;

    public function __construct(\DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        if ($start > $end) {
            throw new \DomainException("End time vocation cannot be less than start");
        }
        $this->start = $start;
        $this->end = $end;
    }

    public static function add(\DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        return new self($start, $end);
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