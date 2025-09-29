<?php

declare(strict_types=1);

namespace App\Domain\Account\Event;

use App\Domain\Account\Account;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Contracts\EventDispatcher\Event;

#[Exclude]
final class AccountRegisteredEvent extends Event
{
    public function __construct(
        private readonly Account $account,
    ) {
    }

    public function getAccount(): Account
    {
        return $this->account;
    }
}
