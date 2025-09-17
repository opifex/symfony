<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\PayPal\RemoteEvent;

use App\Domain\Event\PaymentReceivedEvent;
use Override;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('paypal')]
final class PayPalRemoteEventConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[Override]
    public function consume(RemoteEvent $event): void
    {
        if ($event instanceof PayPalPaymentCaptureEvent) {
            if ($event->getName() === PayPalPaymentCaptureEvent::COMPLETED) {
                $this->eventDispatcher->dispatch(new PaymentReceivedEvent());
            }
        }
    }
}
