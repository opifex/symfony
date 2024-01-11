<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Contract\AccessTokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class AccountAuthenticatedEvent extends Event
{
    public function __construct(public readonly AccessTokenInterface $accessToken)
    {
    }
}
