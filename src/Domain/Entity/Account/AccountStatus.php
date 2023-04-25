<?php

declare(strict_types=1);

namespace App\Domain\Entity\Account;

final class AccountStatus
{
    final public const BLOCKED = 'BLOCKED';

    final public const CREATED = 'CREATED';

    final public const VERIFIED = 'VERIFIED';

    final public const LIST = [
        self::BLOCKED,
        self::CREATED,
        self::VERIFIED,
    ];
}
