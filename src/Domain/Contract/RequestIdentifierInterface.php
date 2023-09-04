<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface RequestIdentifierInterface
{
    public function setIdentifier(string $identifier): void;

    public function getIdentifier(): string;
}
