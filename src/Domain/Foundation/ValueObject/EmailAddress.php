<?php

declare(strict_types=1);

namespace App\Domain\Foundation\ValueObject;

final class EmailAddress
{
    final private function __construct(
        private readonly string $email,
    ) {
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function toString(): string
    {
        return $this->email;
    }

    public function equals(EmailAddress $emailAddress): bool
    {
        return strtolower($this->email) === strtolower($emailAddress->email);
    }
}
