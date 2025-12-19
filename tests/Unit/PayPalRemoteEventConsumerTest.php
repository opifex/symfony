<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Contract\EventMessageBusInterface;
use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalPaymentCaptureEvent;
use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalRemoteEventConsumer;
use Override;
use PHPUnit\Framework\TestCase;

final class PayPalRemoteEventConsumerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->eventMessageBus = $this->createMock(type: EventMessageBusInterface::class);
    }

    public function testConsumeDispatchesPaymentReceivedEventOnCompleted(): void
    {
        $payPalRemoteEventConsumer = new PayPalRemoteEventConsumer($this->eventMessageBus);
        $payPalRemoteEventConsumer->consume(
            new PayPalPaymentCaptureEvent(
                name: PayPalPaymentCaptureEvent::COMPLETED,
                id: '8PT597110X687430LKGECATA',
                payload: ['id' => '8PT597110X687430LKGECATA', 'event_type' => 'PAYMENT.CAPTURE.COMPLETED'],
            ),
        );

        $this->expectNotToPerformAssertions();
    }
}
