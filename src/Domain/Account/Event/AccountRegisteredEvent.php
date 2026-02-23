<?php

declare(strict_types=1);

namespace App\Domain\Account\Event;

use App\Domain\Account\Account;

final class AccountRegisteredEvent
{
    private function __construct(
        public readonly Account $account,
    ) {
    }

    public static function create(Account $account): self
    {
        return new self($account);
    }
}
