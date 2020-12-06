<?php

declare(strict_types=1);

namespace App\Application\Schedule\Command;

use Symfony\Component\Validator\Constraints as Assert;

class ScheduleCommand
{
    /**
     * @Assert\NotBlank(message="Enter Id user")
     * @Assert\GreaterThan(value="0", message="Id user cannot be 0 or negative")
     */
    public int $userId;

    /**
     * @Assert\NotBlank(message="Enter date start")
     * @Assert\Date()
     * @var string|null A "Y-m-d" formatted value
     */
    public ?string $startDate;

    /**
     * @Assert\NotBlank(message="Enter date end")
     * @Assert\Date()
     * @Assert\GreaterThanOrEqual(propertyPath="startDate", message="End date cannot less than start date")
     * @var string|null A "Y-m-d" formatted value
     */
    public ?string $endDate;

    public function __construct(int $userId, ?string $startDate, ?string $endDate)
    {
        $this->userId = $userId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
}