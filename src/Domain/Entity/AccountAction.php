<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class AccountAction
{
    public const string ACTIVATE = 'activate';
    public const string BLOCK = 'block';
    public const string REGISTER = 'register';
    public const string UNBLOCK = 'unblock';
    /** @var string[] */
    public const array ACTIONS = [
        self::ACTIVATE,
        self::BLOCK,
        self::REGISTER,
        self::UNBLOCK,
    ];
}
