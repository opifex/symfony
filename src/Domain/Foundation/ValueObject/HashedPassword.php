<?php

declare(strict_types=1);

namespace App\Domain\Foundation\ValueObject;

final class HashedPassword
{
    final private function __construct(
        private readonly string $passwordHash,
    ) {
    }

    public static function fromString(string $passwordHash): self
    {
        return new self($passwordHash);
    }

    public function toString(): string
    {
        return $this->passwordHash;
    }
}
