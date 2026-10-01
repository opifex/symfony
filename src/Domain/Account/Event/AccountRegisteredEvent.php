<?php

declare(strict_types=1);

namespace App\Domain\Account\Event;

use App\Domain\Account\Account;
use App\Domain\Foundation\Event\EventInterface;

final readonly class AccountRegisteredEvent implements EventInterface
{
    public function __construct(
        public string $accountId,
        public string $accountEmail,
        public string $accountLocale,
    ) {
    }

    public static function create(Account $account): self
    {
        return new self(
            accountId: $account->id->toString(),
            accountEmail: $account->email->toString(),
            accountLocale: $account->locale->toString(),
        );
    }
}
