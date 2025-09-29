<?php

declare(strict_types=1);

namespace App\Domain\Payment\Event;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Contracts\EventDispatcher\Event;

#[Exclude]
final class PaymentReceivedEvent extends Event
{
}
