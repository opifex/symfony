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

        foreach ($object as $constraint) {
            if ($constraint instanceof ConstraintViolationInterface) {
                $messageTemplate = $constraint->getMessageTemplate();
                $violation = [
                    'name' => (new UnicodeString($constraint->getPropertyPath()))->snake()->toString(),
                    'reason' => $this->translator->trans(
                        id: $messageTemplate !== '' ? $messageTemplate : (string)$constraint->getMessage(),
                        parameters: $constraint->getParameters(),
                        domain: 'validators+intl-icu',
                    ),
                ];

                if ($this->kernel->isDebug()) {
                    $violation['object'] = match (true) {
                        is_object($constraint->getRoot()) => $constraint->getRoot()::class,
                        is_string($constraint->getRoot()) => $constraint->getRoot(),
                        default => null,
                    };
                    $violation['value'] = $constraint->getInvalidValue();
                }

                $violationList[] = $violation;
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
}
