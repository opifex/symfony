<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface AccountInterface
{
    public function getUuid(): string;

    public function getEmail(): string;
}
