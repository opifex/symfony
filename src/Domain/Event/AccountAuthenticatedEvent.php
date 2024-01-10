<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Contract\AuthenticationTokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class AccountAuthenticatedEvent extends Event
{
    public function __construct(public readonly AuthenticationTokenInterface $token)
    {
    }
}
