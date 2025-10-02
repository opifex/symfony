<?php

declare(strict_types=1);

namespace App\Domain\Localization;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
enum LocaleCode: string
{
    case EnUs = 'en-US';
    case UkUa = 'uk-UA';

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
