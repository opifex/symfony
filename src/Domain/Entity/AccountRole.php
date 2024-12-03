<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
enum AccountRole: string
{
    case Admin = 'ROLE_ADMIN';
    case User = 'ROLE_USER';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(static fn(self $item) => $item->value, self::cases());
    }

    public static function fromValue(string $value): self
    {
        return self::from($value);
    }
}
