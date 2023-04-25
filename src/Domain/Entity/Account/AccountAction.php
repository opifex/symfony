<?php

declare(strict_types=1);

namespace App\Domain\Entity\Account;

final class AccountAction
{
    final public const BLOCK = 'block';

    final public const UNBLOCK = 'unblock';

    final public const VERIFY = 'verify';

    final public const LIST = [
        self::BLOCK,
        self::UNBLOCK,
        self::VERIFY,
    ];
}
