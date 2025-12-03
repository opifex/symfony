<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalPayloadConverter;
use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalPaymentCaptureEvent;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\RemoteEvent\Exception\ParseException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PayPalPayloadConverterTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->validator = $this->createMock(type: ValidatorInterface::class);
    }

    #[DataProvider(methodName: 'eventTypeDataProvider')]
    public function testConvertWithDifferentPayloads(string $eventType, string $expected): void
    {
        $id = '8PT597110X687430LKGECATA';
        $payPalPayloadConverter = new PayPalPayloadConverter($this->validator);
        $remoteEvent = $payPalPayloadConverter->convert(payload: ['id' => $id, 'event_type' => $eventType]);

        $this->assertSame(expected: $id, actual: $remoteEvent->getId());
        $this->assertSame(expected: $expected, actual: $remoteEvent->getName());
    }

    public function testConvertWithInvalidEventType(): void
    {
        $payPalPayloadConverter = new PayPalPayloadConverter($this->validator);

        $this->expectException(exception: ParseException::class);

        $payPalPayloadConverter->convert(payload: [
            'id' => '8PT597110X687430LKGECATA',
            'event_type' => 'PAYMENT.CAPTURE.UNKNOWN',
        ]);
    }

    public static function eventTypeDataProvider(): iterable
    {
        yield 'a payment capture completes' => [
            'eventType' => 'PAYMENT.CAPTURE.COMPLETED',
            'expected' => PayPalPaymentCaptureEvent::COMPLETED,
        ];
        yield 'a payment capture is declined' => [
            'eventType' => 'PAYMENT.CAPTURE.DECLINED',
            'expected' => PayPalPaymentCaptureEvent::DECLINED,
        ];
        yield 'the state of a payment capture changes to pending' => [
            'eventType' => 'PAYMENT.CAPTURE.PENDING',
            'expected' => PayPalPaymentCaptureEvent::PENDING,
        ];
        yield 'a merchant refunds a payment capture' => [
            'eventType' => 'PAYMENT.CAPTURE.REFUNDED',
            'expected' => PayPalPaymentCaptureEvent::REFUNDED,
        ];
        yield 'paypal reverses a payment capture' => [
            'eventType' => 'PAYMENT.CAPTURE.REVERSED',
            'expected' => PayPalPaymentCaptureEvent::REVERSED,
        ];
    }
}
