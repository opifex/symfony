<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Serializer\TranslationNormalizer;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use stdClass;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslationNormalizerTest extends Unit
{
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->translator = $this->createMock(originalClassName: TranslatorInterface::class);
    }

    public function testGetSupportedTypes(): void
    {
        $translationNormalizer = new TranslationNormalizer($this->translator);
        $supportedTypes = $translationNormalizer->getSupportedTypes(format: null);

        $this->assertArrayHasKey(key: TranslatableMessage::class, array: $supportedTypes);
        $this->assertTrue($supportedTypes[TranslatableMessage::class]);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeWithInvalidObject(): void
    {
        $translationNormalizer = new TranslationNormalizer($this->translator);

        $this->expectException(InvalidArgumentException::class);

        $translationNormalizer->normalize(new stdClass());
    }

    /**
     * @throws ExceptionInterface
     * @throws Exception
     */
    public function testNormalizeWithValidObject(): void
    {
        $translationNormalizer = new TranslationNormalizer($this->translator);
        $message = 'Translatable message';
        $translatedMessage = 'Translated message';

        $this->translator
            ->expects($this->once())
            ->method(constraint: 'trans')
            ->with($message)
            ->willReturn($translatedMessage);

        $normalized = $translationNormalizer->normalize(new TranslatableMessage($message));

        $this->assertSame(expected: $translatedMessage, actual: $normalized);
    }

    public function testSupportsNormalizationWithInvalidObject(): void
    {
        $translationNormalizer = new TranslationNormalizer($this->translator);

        $this->assertFalse($translationNormalizer->supportsNormalization(new stdClass()));
    }

    public function testSupportsNormalizationWithValidObject(): void
    {
        $translationNormalizer = new TranslationNormalizer($this->translator);
        $translatableMessage = new TranslatableMessage(message: 'Translatable message');

        $this->assertTrue($translationNormalizer->supportsNormalization($translatableMessage));
    }
}
