<?php

declare(strict_types=1);

namespace App\Application\Serializer;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ViolationListNormalizer implements NormalizerInterface
{
    public function __construct(
        private KernelInterface $kernel,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array&array<string, mixed> $context
     *
     * @return array<int, mixed>
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        if (!$object instanceof ConstraintViolationListInterface) {
            throw new InvalidArgumentException(message: 'Object expected to be a valid constraint violation list.');
        }

        $violationList = [];

        foreach ($object as $violation) {
            if ($violation instanceof ConstraintViolationInterface) {
                $violationItem = [];
                $violationItem['name'] = $this->formatViolationName($violation);
                $violationItem['reason'] = $this->formatViolationMessage($violation);

                if ($this->kernel->isDebug()) {
                    $violationItem['object'] = $this->extractViolationObject($violation);
                    $violationItem['value'] = $violation->getInvalidValue();
                }

                $violationList[] = $violationItem;
            }
        }

        return $violationList;
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
        return $data instanceof ConstraintViolationListInterface;
    }

    /**
     * @return array<string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [ConstraintViolationListInterface::class => true];
    }

    private function formatViolationName(ConstraintViolationInterface $violation): string
    {
        return (new UnicodeString($violation->getPropertyPath()))->snake()->toString();
    }

    private function formatViolationMessage(ConstraintViolationInterface $violation): string
    {
        $messageTemplate = $violation->getMessageTemplate();
        $message = $messageTemplate !== '' ? $messageTemplate : (string)$violation->getMessage();

        return $this->translator->trans($message, $violation->getParameters(), domain: 'validators+intl-icu');
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
