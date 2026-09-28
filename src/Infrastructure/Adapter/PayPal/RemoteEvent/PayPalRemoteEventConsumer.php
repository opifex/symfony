<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\PayPal\RemoteEvent;

use Override;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('paypal')]
final readonly class PayPalRemoteEventConsumer implements ConsumerInterface
{
    #[Override]
    public function consume(RemoteEvent $event): void
    {
        if ($event instanceof PayPalPaymentCaptureEvent) {
            if ($event->getName() === PayPalPaymentCaptureEvent::COMPLETED) {
                return;
            }
        }
    }
}
