<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
enum AccountAction: string
{
    case Activate = 'activate';
    case Block = 'block';
    case Register = 'register';
    case Unblock = 'unblock';

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
