<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalPaymentCaptureEvent;
use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalRemoteEventConsumer;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class PayPalRemoteEventConsumerTest extends TestCase
{
    private EventDispatcherInterface&MockObject $eventDispatcher;

    #[Override]
    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(type: EventDispatcherInterface::class);
    }

    public function testConsumeDispatchesPaymentReceivedEventOnCompleted(): void
    {
        $payPalRemoteEventConsumer = new PayPalRemoteEventConsumer($this->eventDispatcher);
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
