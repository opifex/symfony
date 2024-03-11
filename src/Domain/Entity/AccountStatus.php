<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class AccountStatus
{
    public const string ACTIVATED = 'activated';
    public const string BLOCKED = 'blocked';
    public const string CREATED = 'created';
    public const string REGISTERED = 'registered';
    /** @var string[] */
    public const array STATUSES = [
        self::ACTIVATED,
        self::BLOCKED,
        self::CREATED,
        self::REGISTERED,
    ];
}
