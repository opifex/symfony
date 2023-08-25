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

        $this->translationNormalizer = new TranslationNormalizer($this->translator);
    }

    public function testGetSupportedTypes(): void
    {
        $supportedTypes = $this->translationNormalizer->getSupportedTypes(format: null);

        $this->assertEquals(expected: [TranslatableMessage::class => true], actual: $supportedTypes);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeWithInvalidObject(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->translationNormalizer->normalize(new stdClass());
    }

    /**
     * @throws ExceptionInterface
     * @throws Exception
     */
    public function testNormalizeWithValidObject(): void
    {
        $message = 'Translatable message';
        $translatedMessage = 'Translated message';

        $this->translator
            ->expects($this->once())
            ->method(constraint: 'trans')
            ->with($message)
            ->willReturn($translatedMessage);

        $normalized = $this->translationNormalizer->normalize(new TranslatableMessage($message));

        $this->assertSame(expected: $translatedMessage, actual: $normalized);
    }

    public function testSupportsNormalizationWithInvalidObject(): void
    {
        $this->assertFalse($this->translationNormalizer->supportsNormalization(new stdClass()));
    }

    public function testSupportsNormalizationWithValidObject(): void
    {
        $translatableMessage = new TranslatableMessage(message: 'Translatable message');
        $supports = $this->translationNormalizer->supportsNormalization($translatableMessage);

        $this->assertTrue($supports);
    }
}
