<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Serializer\HtmlTemplateEncoder;
use App\Domain\Contract\TemplateRendererInterface;
use App\Domain\Exception\TemplateRendererException;
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
        $this->templateRenderer = $this->createMock(originalClassName: TemplateRendererInterface::class);
    }

    public function testEncodeWithExistedTemplate(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->templateRenderer);
        $content = 'content';

        $this->templateRenderer
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
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->templateRenderer);

        $this->expectException(InvalidArgumentException::class);

        $htmlTemplateEncoder->encode(
            data: new stdClass(),
            format: HtmlTemplateEncoder::FORMAT,
            context: [HtmlTemplateEncoder::TEMPLATE => 'example.html.twig'],
        );
    }

    public function testEncodeThrowsExceptionWithoutTemplate(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->templateRenderer);

        $this->expectException(InvalidArgumentException::class);

        $htmlTemplateEncoder->encode(data: [], format: HtmlTemplateEncoder::FORMAT);
    }

    public function testEncodeThrowsExceptionOnTwigAdapterError(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->templateRenderer);

        $this->templateRenderer
            ->expects($this->once())
            ->method(constraint: 'render')
            ->willThrowException(new TemplateRendererException());

        $this->expectException(RuntimeException::class);

        $htmlTemplateEncoder->encode(
            data: [],
            format: HtmlTemplateEncoder::FORMAT,
            context: [HtmlTemplateEncoder::TEMPLATE => 'example.html.twig'],
        );
    }

    public function testCheckSupportsEncoding(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->templateRenderer);

        $this->assertTrue($htmlTemplateEncoder->supportsEncoding(format: HtmlTemplateEncoder::FORMAT));
        $this->assertFalse($htmlTemplateEncoder->supportsEncoding(format: ''));
    }
}
