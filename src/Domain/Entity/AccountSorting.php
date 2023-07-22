<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class AccountSorting
{
    public const CREATED_AT = 'created_at';

    public const EMAIL = 'email';

    public const STATUS = 'status';

    public const LIST = [
        self::CREATED_AT,
        self::EMAIL,
        self::STATUS,
    ];
}
