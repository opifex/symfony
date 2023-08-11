<?php

declare(strict_types=1);

namespace App\Application\Serializer;

use App\Domain\Exception\ValidationFailedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Throwable;

final class ExceptionNormalizer implements NormalizerInterface
{
    private const TRANSLATOR_DOMAIN = 'exceptions';

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

        $domain = self::TRANSLATOR_DOMAIN . MessageCatalogueInterface::INTL_DOMAIN_SUFFIX;

        $exception = [
            'code' => $object instanceof HttpException ? $object->getStatusCode() : $object->getCode(),
            'message' => new TranslatableMessage($object->getMessage(), domain: $domain),
        ];

        if ($object instanceof ValidationFailedHttpException) {
            $exception['violations'] = $object->getViolations();
        }

        if ($this->kernel->isDebug()) {
            $trace = fn($e): array => ['file' => $e->getFile(), 'type' => $e::class, 'line' => $e->getLine()];
            $filterPrevious = $object->getPrevious() ? $trace($object->getPrevious()) : [];
            $exception['trace'] = array_filter([$trace($object), $filterPrevious]);
        }

        return $exception;
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
