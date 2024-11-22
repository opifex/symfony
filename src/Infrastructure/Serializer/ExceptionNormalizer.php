<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer;

use Override;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

final class ExceptionNormalizer implements NormalizerInterface
{
    private const string TRANSLATOR_DOMAIN = 'exceptions';

    public function __construct(
        private KernelInterface $kernel,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array&array<string, mixed> $context
     * @return array<string, mixed>
     */
    #[Override]
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        if (!$object instanceof Throwable) {
            $object = new InvalidArgumentException(message: 'Object expected to be a valid exception type.');
        }

        $exception = [
            'code' => $this->generateExceptionCode($object),
            'error' => $this->localizeExceptionMessage($object),
        ];

        if (method_exists($object, method: 'getViolations')) {
            $exception['violations'] = [];

            /** @var ConstraintViolationInterface $violation */
            foreach ($object->getViolations() as $violation) {
                $violationItem = [
                    'name' => $this->formatViolationName($violation),
                    'reason' => $this->formatViolationMessage($violation),
                ];

                if ($this->kernel->isDebug()) {
                    $violationItem['object'] = $this->extractViolationObject($violation);
                    $violationItem['value'] = $violation->getInvalidValue();
                }

                $exception['violations'][] = $violationItem;
            }
        }

        if ($this->kernel->isDebug()) {
            $trace = static fn($e): array => ['file' => $e->getFile(), 'type' => $e::class, 'line' => $e->getLine()];
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
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
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

    private function generateExceptionCode(Throwable $object): string
    {
        return Uuid::v5(Uuid::fromString(uuid: Uuid::NAMESPACE_OID), $object::class)->toRfc4122();
    }

    private function localizeExceptionMessage(Throwable $object): string
    {
        $domain = self::TRANSLATOR_DOMAIN . MessageCatalogueInterface::INTL_DOMAIN_SUFFIX;

        return $this->translator->trans($object->getMessage(), domain: $domain);
    }

    private function formatViolationName(ConstraintViolationInterface $violation): string
    {
        return new UnicodeString($violation->getPropertyPath())->snake()->toString();
    }

    private function formatViolationMessage(ConstraintViolationInterface $violation): string
    {
        $domain = self::TRANSLATOR_DOMAIN . MessageCatalogueInterface::INTL_DOMAIN_SUFFIX;

        return $this->translator->trans((string) $violation->getMessage(), $violation->getParameters(), $domain);
    }

    private function extractViolationObject(ConstraintViolationInterface $violation): ?string
    {
        return match (true) {
            is_object($violation->getRoot()) => $violation->getRoot()::class,
            is_string($violation->getRoot()) => $violation->getRoot(),
            default => null,
        };
    }
}
