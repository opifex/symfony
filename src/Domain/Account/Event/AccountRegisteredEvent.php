<?php

declare(strict_types=1);

namespace App\Domain\Account\Event;

use App\Domain\Account\Account;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Contracts\EventDispatcher\Event;

#[Exclude]
final class AccountRegisteredEvent extends Event
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
