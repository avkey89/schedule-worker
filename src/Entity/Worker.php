<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\VO\Schedule;

/**
 * @ORM\Entity()
 */
class Worker
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $name;

    /**
     * @ORM\Embedded(class="App\Entity\VO\Schedule", columnPrefix=false)
     */
    private Schedule $scheduleWorkDay;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vacation", cascade={"remove"}, mappedBy="worker")
     */
    private array $vacation;

    public function __construct(int $id, string $name, Schedule $scheduleWorkDay)
    {
        if (empty($id)) {
            throw new \DomainException("Please enter the ID");
        }
        if ($id < 0) {
            throw new \DomainException("The ID don't be negative");
        }
        $this->id = $id;
        $this->name = $name;
        $this->scheduleWorkDay = $scheduleWorkDay;
        $this->vacation = [];
    }

    public static function create(int $id, string $name, Schedule $scheduleWorkDay): self
    {
        return new self($id, $name, $scheduleWorkDay);
    }

    public function addVacation(Vacation $vacation): self
    {
        $this->vacation[] = $vacation;

        return $this;
    }

    public function getData(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "scheduleWorkDay" => $this->scheduleWorkDay->getData(),
            "vacation" => $this->vacation
        ];
    }
}