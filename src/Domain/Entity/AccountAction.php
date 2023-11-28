<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class AccountAction
{
    public const string BLOCK = 'block';
    public const string UNBLOCK = 'unblock';
    public const string VERIFY = 'verify';
    /** @var string[] */
    public const array ACTIONS = [
        self::BLOCK,
        self::UNBLOCK,
        self::VERIFY,
    ];
}
