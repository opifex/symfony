<?php

declare(strict_types=1);

namespace App\Domain\Payment\Event;

final class PaymentReceivedEvent
{
    public static function create(): self
    {
        return new self();
    }
}
