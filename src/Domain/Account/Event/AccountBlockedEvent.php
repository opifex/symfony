<?php

declare(strict_types=1);

namespace App\Domain\Account\Event;

use App\Domain\Account\Account;
use App\Domain\Foundation\Event\EventInterface;

final readonly class AccountBlockedEvent implements EventInterface
{
    private function __construct(
        public Account $account,
    ) {
    }

    public static function create(Account $account): self
    {
        return new self($account);
    }
}
