<?php

declare(strict_types=1);

namespace App\Domain\Contract;

interface IdentityManagerInterface
{
    public function validateIdentifier(string $identifier): bool;

    public function changeIdentifier(string $identifier): void;

    public function extractIdentifier(): string;
}
