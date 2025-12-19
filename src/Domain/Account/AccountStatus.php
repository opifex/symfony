<?php

declare(strict_types=1);

namespace App\Domain\Account;

enum AccountStatus: string
{
    case Activated = 'activated';
    case Blocked = 'blocked';
    case Created = 'created';
    case Registered = 'registered';

    public static function fromString(string $value): self
    {
        return self::from($value);
    }

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(static fn(self $item) => $item->value, self::cases());
    }

    public function toString(): string
    {
        return $this->value;
    }
}
