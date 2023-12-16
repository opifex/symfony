<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Locale
{
    public const string DEFAULT = self::EN;
    public const string EN = 'en';
    public const string UK = 'uk';
    /** @var string[] */
    public const array LOCALES = [
        self::EN,
        self::UK,
    ];
}
