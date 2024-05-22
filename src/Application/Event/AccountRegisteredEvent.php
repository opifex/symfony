<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Entity\Account;
use Symfony\Contracts\EventDispatcher\Event;

final class AccountRegisteredEvent extends Event
{
    public function __construct(public readonly Account $account)
    {
    }
}
