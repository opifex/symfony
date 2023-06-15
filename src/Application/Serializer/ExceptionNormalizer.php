<?php

declare(strict_types=1);

namespace App\Application\Serializer;

use App\Domain\Exception\AbstractHttpException;
use App\Domain\Exception\ExtraAttributesHttpException;
use App\Domain\Exception\NormalizationFailedHttpException;
use App\Domain\Exception\ValidationFailedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\ValidationFailedException as MessengerValidationFailedException;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Exception\ValidationFailedException as ValidatorValidationFailedException;
use Throwable;

final class ExceptionNormalizer implements NormalizerInterface
{
    public function __construct(private KernelInterface $kernel)
    {
    }

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

        $debug = $this->kernel->isDebug();
        $trace = fn($e): array => ['file' => $e->getFile(), 'type' => $e::class, 'line' => $e->getLine()];
        $object = $object instanceof HandlerFailedException ? ($object->getPrevious() ?? $object) : $object;

        if ($object instanceof MessengerValidationFailedException) {
            $object = new ValidationFailedHttpException($object->getViolations(), $debug);
        } elseif ($object instanceof NotNormalizableValueException) {
            $object = new NormalizationFailedHttpException($object->getExpectedTypes(), $object->getPath(), $debug);
        } elseif ($object instanceof ExtraAttributesException) {
            $object = new ExtraAttributesHttpException($object->getExtraAttributes(), $debug);
        } elseif ($object instanceof ValidatorValidationFailedException) {
            $object = new ValidationFailedHttpException($object->getViolations(), $debug);
        }

        $code = $object instanceof HttpException ? $object->getStatusCode() : (int)$object->getCode();
        $context = $object instanceof AbstractHttpException ? $object->getContext() : [];

        if ($debug) {
            $filterPrevious = $object->getPrevious() ? $trace($object->getPrevious()) : [];
            $context['trace'] = array_filter([$trace($object), $filterPrevious]);
        }

        return [
            'code' => $code < 100 || $code >= 600 ? 500 : $code,
            'message' => new TranslatableMessage($object->getMessage(), domain: 'exceptions+intl-icu'),
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
        return [Throwable::class => true];
    }
}
