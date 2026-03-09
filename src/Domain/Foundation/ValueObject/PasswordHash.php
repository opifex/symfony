<?php

declare(strict_types=1);

namespace App\Domain\Foundation\ValueObject;

final readonly class PasswordHash
{
    private function __construct(
        private string $passwordHash,
    ) {
    }

    public static function fromString(string $passwordHash): self
    {
        return new self(trim($passwordHash));
    }

    public function toString(): string
    {
        return $this->passwordHash;
    }
}
