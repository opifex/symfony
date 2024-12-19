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
        private readonly KernelInterface $kernel,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    #[Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        if (!$data instanceof Throwable) {
            $data = new InvalidArgumentException(message: 'Object expected to be a valid exception type.');
        }

        $exception = [
            'code' => $this->generateExceptionCode($data),
            'error' => $this->localizeExceptionMessage($data),
        ];

        if (method_exists($data, method: 'getViolations')) {
            $exception['violations'] = [];

            /** @var ConstraintViolationInterface $violation */
            foreach ($data->getViolations() as $violation) {
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
            $filterPrevious = $data->getPrevious() ? $trace($data->getPrevious()) : [];
            $exception['trace'] = array_filter([$trace($data), $filterPrevious]);
        }

        return $exception;
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array<string, mixed> $context
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

    private function generateExceptionCode(Throwable $throwable): string
    {
        return Uuid::v5(Uuid::fromString(uuid: Uuid::NAMESPACE_OID), $throwable::class)->toRfc4122();
    }

    private function localizeExceptionMessage(Throwable $throwable): string
    {
        $domain = self::TRANSLATOR_DOMAIN . MessageCatalogueInterface::INTL_DOMAIN_SUFFIX;

        return $this->translator->trans($throwable->getMessage(), domain: $domain);
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
