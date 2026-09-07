<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Domain\Payment\Event\PaymentReceivedEvent;
use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalPaymentCaptureEvent;
use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalRemoteEventConsumer;
use App\Infrastructure\Messenger\MessageBus\EventMessageBus;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class PayPalRemoteEventConsumerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(type: MessageBusInterface::class);
        $this->eventMessageBus = new EventMessageBus($this->messageBus);
    }

    public function testConsumeDispatchesPaymentReceivedEventOnCompleted(): void
    {
        $this->messageBus
            ->expects($this->once())
            ->method(constraint: 'dispatch')
            ->with($this->isInstanceOf(PaymentReceivedEvent::class))
            ->willReturnCallback(fn (object $event) => new Envelope($event));

        $payPalRemoteEventConsumer = new PayPalRemoteEventConsumer($this->eventMessageBus);
        $payPalRemoteEventConsumer->consume(
            new PayPalPaymentCaptureEvent(
                name: PayPalPaymentCaptureEvent::COMPLETED,
                id: '8PT597110X687430LKGECATA',
                payload: ['id' => '8PT597110X687430LKGECATA', 'event_type' => 'PAYMENT.CAPTURE.COMPLETED'],
            ),
        );
    }
}
