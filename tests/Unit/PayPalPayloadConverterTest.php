<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalPayloadConverter;
use App\Infrastructure\Adapter\PayPal\RemoteEvent\PayPalPaymentCaptureEvent;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\RemoteEvent\Exception\ParseException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PayPalPayloadConverterTest extends TestCase
{
    private ValidatorInterface&MockObject $validator;

    #[Override]
    protected function setUp(): void
    {
        $this->validator = $this->createMock(type: ValidatorInterface::class);
    }

    #[DataProvider(methodName: 'eventTypeDataProvider')]
    public function testConvertWithDifferentPayloads(string $value, string $expected): void
    {
        $id = '8PT597110X687430LKGECATA';
        $payPalPayloadConverter = new PayPalPayloadConverter($this->validator);
        $remoteEvent = $payPalPayloadConverter->convert(payload: ['id' => $id, 'event_type' => $value]);
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
        return [
            ['value' => 'PAYMENT.CAPTURE.DECLINED', 'expected' => PayPalPaymentCaptureEvent::DECLINED],
            ['value' => 'PAYMENT.CAPTURE.COMPLETED', 'expected' => PayPalPaymentCaptureEvent::COMPLETED],
            ['value' => 'PAYMENT.CAPTURE.PENDING', 'expected' => PayPalPaymentCaptureEvent::PENDING],
            ['value' => 'PAYMENT.CAPTURE.REFUNDED', 'expected' => PayPalPaymentCaptureEvent::REFUNDED],
            ['value' => 'PAYMENT.CAPTURE.REVERSED', 'expected' => PayPalPaymentCaptureEvent::REVERSED],
        ];
    }
}
