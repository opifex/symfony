<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class LocaleCode
{
    public const string EN = 'en';
    public const string UK = 'uk';
    /** @var string[] */
    public const array CODES = [
        self::EN,
        self::UK,
    ];
}
