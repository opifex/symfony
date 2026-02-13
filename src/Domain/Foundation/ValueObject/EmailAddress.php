<?php

declare(strict_types=1);

namespace App\Domain\Foundation\ValueObject;

use DomainException;

final class EmailAddress
{
    final private function __construct(
        private readonly string $email,
    ) {
    }

    public static function fromString(string $email): self
    {
        $email = strtolower(trim($email));

        if (filter_var($email, filter: FILTER_VALIDATE_EMAIL) === false) {
            throw new DomainException(message: 'Invalid email address provided.');
        }

        return new self($email);
    }

    public function toString(): string
    {
        return $this->email;
    }

    public function equals(self $emailAddress): bool
    {
        return $this->email === $emailAddress->email;
    }
}
