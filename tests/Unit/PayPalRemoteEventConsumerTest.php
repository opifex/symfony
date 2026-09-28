<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalPaymentCaptureEvent;
use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalRemoteEventConsumer;
use PHPUnit\Framework\TestCase;

final class PayPalRemoteEventConsumerTest extends TestCase
{
    public function testConsumeDoesNothingOnCompleted(): void
    {
        $this->expectNotToPerformAssertions();

        $payPalRemoteEventConsumer = new PayPalRemoteEventConsumer();
        $payPalRemoteEventConsumer->consume(
            new PayPalPaymentCaptureEvent(
                name: PayPalPaymentCaptureEvent::COMPLETED,
                id: '8PT597110X687430LKGECATA',
                payload: ['id' => '8PT597110X687430LKGECATA', 'event_type' => 'PAYMENT.CAPTURE.COMPLETED'],
            ),
        );
    }
}
