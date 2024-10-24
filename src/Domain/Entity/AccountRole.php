<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountRole
{
    public const string ROLE_ADMIN = 'ROLE_ADMIN';
    public const string ROLE_USER = 'ROLE_USER';
    /** @var string[] */
    public const array ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
    ];
}
