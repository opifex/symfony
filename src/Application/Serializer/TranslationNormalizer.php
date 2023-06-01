<?php

declare(strict_types=1);

namespace App\Application\Serializer;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslationNormalizer implements NormalizerInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array&array<string, mixed> $context
     *
     * @return string
     */
    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        if (!$object instanceof TranslatableMessage) {
            throw new InvalidArgumentException(message: 'Object expected to be a valid translatable message.');
        }

        return $object->trans($this->translator);
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
        return $data instanceof TranslatableMessage;
    }

    /**
     * @return array<string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            TranslatableMessage::class => true,
        ];
    }
}
