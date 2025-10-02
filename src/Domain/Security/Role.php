<?php

declare(strict_types=1);

namespace App\Domain\Security;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
enum Role: string
{
    case Admin = 'ROLE_ADMIN';
    case User = 'ROLE_USER';

    public static function fromString(string $value): self
    {
        return self::from($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
