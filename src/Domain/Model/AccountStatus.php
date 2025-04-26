<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountStatus
{
    public const string ACTIVATED = 'activated';
    public const string BLOCKED = 'blocked';
    public const string CREATED = 'created';
    public const string REGISTERED = 'registered';
    // List of all available account statuses
    public const array CASES = [
        self::ACTIVATED,
        self::BLOCKED,
        self::CREATED,
        self::REGISTERED,
    ];
}
