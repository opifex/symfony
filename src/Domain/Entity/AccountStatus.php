<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
enum AccountStatus: string
{
    case Activated = 'activated';
    case Blocked = 'blocked';
    case Created = 'created';
    case Registered = 'registered';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(static fn(self $item): string => $item->value, self::cases());
    }

    public static function fromValue(string $value): self
    {
        return self::from($value);
    }
}
