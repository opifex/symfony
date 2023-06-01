<?php

declare(strict_types=1);

namespace App\Application\Serializer;

use App\Domain\Exception\AbstractHttpException;
use App\Domain\Exception\ExtraAttributesHttpException;
use App\Domain\Exception\NormalizationFailedHttpException;
use App\Domain\Exception\ValidationFailedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\ValidationFailedException as MessengerValidationFailedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Exception\ValidationFailedException as ValidatorValidationFailedException;
use Throwable;

final class ExceptionNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $object
     * @param string|null $format
     * @param array&array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Throwable) {
            $object = new InvalidArgumentException(message: 'Object expected to be a valid exception type.');
        }

        $object = $object instanceof HandlerFailedException ? ($object->getPrevious() ?? $object) : $object;
        $previous = $object->getPrevious();

        if ($object instanceof MessengerValidationFailedException) {
            $object = new ValidationFailedHttpException($object->getViolations());
        } elseif ($object instanceof NotNormalizableValueException) {
            $object = new NormalizationFailedHttpException($object->getExpectedTypes(), $object->getPath());
        } elseif ($object instanceof ExtraAttributesException) {
            $object = new ExtraAttributesHttpException($object->getExtraAttributes());
        } elseif ($object instanceof ValidatorValidationFailedException) {
            $object = new ValidationFailedHttpException($object->getViolations());
        }

        $message = $object->getMessage();
        $code = $object instanceof HttpException ? $object->getStatusCode() : (int)$object->getCode();
        $context = $object instanceof AbstractHttpException ? $object->getContext() : [];
        $trace = fn($e): array => ['file' => $e->getFile(), 'type' => $e::class, 'line' => $e->getLine()];
        $filterPrevious = $object->getPrevious() ? $trace($object->getPrevious()) : [];
        $context['trace'] = array_filter([$trace($object), $filterPrevious]);

        if ($previous instanceof AuthenticationException) {
            $message = $previous->getMessage() ?: $previous->getMessageKey();
        }

        return [
            'code' => $code < 100 || $code >= 600 ? 500 : $code,
            'message' => new TranslatableMessage($message, domain: 'exceptions+intl-icu'),
            ...$context,
        ];
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array&array<string, mixed> $context
     *
     * @return bool
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof Throwable;
    }

    /**
     * @return array<string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            Throwable::class => true,
        ];
    }
}
