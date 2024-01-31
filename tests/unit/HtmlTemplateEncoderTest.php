<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Serializer\HtmlTemplateEncoder;
use App\Domain\Contract\TemplateEngineInterface;
use App\Domain\Exception\TemplateEngineException;
use Codeception\Test\Unit;
use InvalidArgumentException;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use RuntimeException;
use stdClass;

final class HtmlTemplateEncoderTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->templateEngine = $this->createMock(originalClassName: TemplateEngineInterface::class);
    }

    public function testEncodeWithExistedTemplate(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->templateEngine);
        $content = 'content';

        $this->templateEngine
            ->expects($this->once())
            ->method(constraint: 'render')
            ->willReturn($content);

        $encoded = $htmlTemplateEncoder->encode(
            data: [],
            format: HtmlTemplateEncoder::FORMAT,
            context: [HtmlTemplateEncoder::TEMPLATE => 'example.html.twig'],
        );

        $this->assertSame($content, $encoded);
    }

    public function testEncodeThrowsExceptionWithInvalidData(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->templateEngine);

        $this->expectException(InvalidArgumentException::class);

        $htmlTemplateEncoder->encode(
            data: new stdClass(),
            format: HtmlTemplateEncoder::FORMAT,
            context: [HtmlTemplateEncoder::TEMPLATE => 'example.html.twig'],
        );
    }

    public function testEncodeThrowsExceptionWithoutTemplate(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->templateEngine);

        $this->expectException(InvalidArgumentException::class);

        $htmlTemplateEncoder->encode(data: [], format: HtmlTemplateEncoder::FORMAT);
    }

    public function testEncodeThrowsExceptionOnTwigAdapterError(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->templateEngine);

        $this->templateEngine
            ->expects($this->once())
            ->method(constraint: 'render')
            ->willThrowException(new TemplateEngineException());

        $this->expectException(RuntimeException::class);

        $htmlTemplateEncoder->encode(
            data: [],
            format: HtmlTemplateEncoder::FORMAT,
            context: [HtmlTemplateEncoder::TEMPLATE => 'example.html.twig'],
        );
    }

    public function testCheckSupportsEncoding(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->templateEngine);

        $this->assertTrue($htmlTemplateEncoder->supportsEncoding(format: HtmlTemplateEncoder::FORMAT));
        $this->assertFalse($htmlTemplateEncoder->supportsEncoding(format: ''));
    }
}
