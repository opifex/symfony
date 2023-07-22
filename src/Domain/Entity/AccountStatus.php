<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class AccountStatus
{
    public const BLOCKED = 'blocked';

    public const CREATED = 'created';

    public const VERIFIED = 'verified';

    public const LIST = [
        self::BLOCKED,
        self::CREATED,
        self::VERIFIED,
    ];
}
