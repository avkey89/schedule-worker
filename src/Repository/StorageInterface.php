<?php

declare(strict_types=1);

namespace App\Repository;

interface StorageInterface
{
    public function find(int $id): ?object;

    public function all(): ?array;
}