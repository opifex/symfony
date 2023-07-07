<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Contract\AccountInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class AccountCreateEvent extends Event
{
    public function __construct(public readonly AccountInterface $account)
    {
    }
}
