<?php

declare(strict_types=1);

namespace App\Domain\Account\Event;

use App\Domain\Account\Account;

class AccountRegisteredEvent
{
    private function __construct(
        private readonly Account $account,
    ) {
    }

    public static function create(Account $account): self
    {
        return new self($account);
    }

    public function getAccount(): Account
    {
        return $this->account;
    }
}
