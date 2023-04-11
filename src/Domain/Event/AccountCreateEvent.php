<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Entity\Account\Account;
use Symfony\Contracts\EventDispatcher\Event;

class AccountCreateEvent extends Event
{
    public function __construct(public Account $account)
    {
    }
}
