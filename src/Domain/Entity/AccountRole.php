<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class AccountRole
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_USER = 'ROLE_USER';
    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
    ];
}
