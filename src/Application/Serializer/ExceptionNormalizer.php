<?php

declare(strict_types=1);

namespace App\Application\Serializer;

use App\Domain\Exception\ValidationFailedException;
use Override;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Uid\Uuid;
use Throwable;

final class ExceptionNormalizer implements NormalizerInterface
{
    private const string TRANSLATOR_DOMAIN = 'exceptions';

    public function __construct(private KernelInterface $kernel)
    {
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array&array<string, mixed> $context
     * @return array<string, mixed>
     */
    #[Override]
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Throwable) {
            $object = new InvalidArgumentException(message: 'Object expected to be a valid exception type.');
        }

        $exception = [
            'code' => $this->generateExceptionCode($object),
            'error' => $this->localizeExceptionMessage($object),
        ];

        if ($object instanceof ValidationFailedException) {
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
     * @return bool
     */
    #[Override]
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof Throwable;
    }

    /**
     * @return array<string, bool>
     */
    #[Override]
    public function getSupportedTypes(?string $format): array
    {
        return [Throwable::class => true];
    }

    private function generateExceptionCode(Throwable $object): Uuid
    {
        return Uuid::v5(Uuid::fromString(uuid: Uuid::NAMESPACE_OID), $object::class);
    }

    private function localizeExceptionMessage(Throwable $object): TranslatableMessage
    {
        $domain = self::TRANSLATOR_DOMAIN . MessageCatalogueInterface::INTL_DOMAIN_SUFFIX;

        return new TranslatableMessage($object->getMessage(), domain: $domain);
    }
}
