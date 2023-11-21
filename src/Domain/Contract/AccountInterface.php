<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use DateTimeInterface;

interface AccountInterface
{
    public function getUuid(): string;

    public function getCreatedAt(): DateTimeInterface;

    public function getEmail(): string;

    public function getLocale(): string;

    /**
     * @return string[]
     */
    public function getRoles(): array;

    public function getStatus(): string;
}
