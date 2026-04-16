<?php

declare(strict_types=1);

namespace App\Domain\Payment\Event;

final readonly class PaymentReceivedEvent
{
    public static function create(): self
    {
        return new self();
    }
}
