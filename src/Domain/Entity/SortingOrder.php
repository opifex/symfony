<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
enum SortingOrder: string
{
    case Asc = 'asc';
    case Desc = 'desc';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(static fn(self $order) => $order->value, self::cases());
    }

    public static function fromValue(string $value): self
    {
        return self::from($value);
    }
}
