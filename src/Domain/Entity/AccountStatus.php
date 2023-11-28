<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class AccountStatus
{
    public const string BLOCKED = 'blocked';
    public const string CREATED = 'created';
    public const string VERIFIED = 'verified';
    /** @var string[] */
    public const array STATUSES = [
        self::BLOCKED,
        self::CREATED,
        self::VERIFIED,
    ];
}
