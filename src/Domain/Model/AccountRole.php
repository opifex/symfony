<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountRole
{
    public const string ADMIN = 'ROLE_ADMIN';
    public const string USER = 'ROLE_USER';
    // List of all available account roles
    public const array CASES = [
        self::ADMIN,
        self::USER,
    ];
}
