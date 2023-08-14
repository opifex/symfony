<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface MessageIdentifierInterface
{
    public function validate(string $identifier): bool;

    public function replace(string $identifier): void;

    public function identify(): string;
}
