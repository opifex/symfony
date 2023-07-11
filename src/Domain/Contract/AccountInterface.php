<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use DateTimeInterface;

interface AccountInterface
{
    public function getUuid(): string;

    public function getEmail(): string;

    public function getStatus(): string;

    /**
     * @return string[]
     */
    public function getRoles(): array;

    public function getCreatedAt(): DateTimeInterface;
}
