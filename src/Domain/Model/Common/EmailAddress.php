<?php

declare(strict_types=1);

namespace App\Domain\Model\Common;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class EmailAddress
{
    final protected function __construct(
        private readonly string $email,
    ) {
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function equals(EmailAddress $emailAddress): bool
    {
        return strtolower($this->email) === strtolower($emailAddress->email);
    }

    public function toString(): string
    {
        return $this->email;
    }
}
