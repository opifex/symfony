<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class AccountStatus
{
    public const BLOCKED = 'BLOCKED';

    public const CREATED = 'CREATED';

    public const VERIFIED = 'VERIFIED';

    public const LIST = [
        self::BLOCKED,
        self::CREATED,
        self::VERIFIED,
    ];
}
