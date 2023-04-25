<?php

declare(strict_types=1);

namespace App\Domain\Entity\Account;

final class AccountRole
{
    final public const ROLE_ADMIN = 'ROLE_ADMIN';

    final public const ROLE_USER = 'ROLE_USER';

    final public const LIST = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
    ];
}
