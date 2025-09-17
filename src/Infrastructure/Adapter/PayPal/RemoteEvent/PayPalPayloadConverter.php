<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\PayPal\RemoteEvent;

use Override;
use Symfony\Component\RemoteEvent\Exception\ParseException;
use Symfony\Component\RemoteEvent\PayloadConverterInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PayPalPayloadConverter implements PayloadConverterInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @param array{id: string, event_type: string} $payload
     */
    #[Override]
    public function convert(array $payload): RemoteEvent
    {
        $violations = $this->validator->validate($payload, new Assert\Collection([
            'id' => [new Assert\NotBlank(), new Assert\Type(type: 'string')],
            'event_type' => [new Assert\NotBlank(), new Assert\Type(type: 'string')],
        ], allowExtraFields: true));

        if ($violations->count()) {
            $violationsList = array_map(
                callback: fn(ConstraintViolationInterface $violation) => implode(separator: ' ', array: [
                    $violation->getPropertyPath(),
                    $violation->getMessage(),
                ]),
                array: [...$violations],
            );
            throw new ParseException(sprintf('Invalid payload. %s', implode(separator: ' ', array: $violationsList)));
        }

        $name = match ($payload['event_type']) {
            'PAYMENT.CAPTURE.DECLINED' => PayPalPaymentCaptureEvent::DECLINED,
            'PAYMENT.CAPTURE.COMPLETED' => PayPalPaymentCaptureEvent::COMPLETED,
            'PAYMENT.CAPTURE.PENDING' => PayPalPaymentCaptureEvent::PENDING,
            'PAYMENT.CAPTURE.REFUNDED' => PayPalPaymentCaptureEvent::REFUNDED,
            'PAYMENT.CAPTURE.REVERSED' => PayPalPaymentCaptureEvent::REVERSED,
            default => throw new ParseException(sprintf('Unsupported event type "%s".', $payload['event_type'])),
        };

        return new PayPalPaymentCaptureEvent($name, $payload['id'], $payload);
    }
}
