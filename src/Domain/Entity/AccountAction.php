<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class AccountAction
{
    public const BLOCK = 'block';

    public const UNBLOCK = 'unblock';

    public const VERIFY = 'verify';

    public const LIST = [
        self::BLOCK,
        self::UNBLOCK,
        self::VERIFY,
    ];
}
