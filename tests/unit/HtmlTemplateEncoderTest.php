<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Serializer\HtmlTemplateEncoder;
use App\Domain\Contract\TwigAdapterInterface;
use App\Domain\Exception\TwigAdapterException;
use Codeception\Test\Unit;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Exception;
use RuntimeException;
use stdClass;

final class HtmlTemplateEncoderTest extends Unit
{
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->twigAdapter = $this->createMock(originalClassName: TwigAdapterInterface::class);
    }

    public function testEncodeWithExistedTemplate(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->twigAdapter);
        $content = 'content';

        $this->twigAdapter
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
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->twigAdapter);

        $this->expectException(InvalidArgumentException::class);

        $htmlTemplateEncoder->encode(
            data: new stdClass(),
            format: HtmlTemplateEncoder::FORMAT,
            context: [HtmlTemplateEncoder::TEMPLATE => 'example.html.twig'],
        );
    }

    public function testEncodeThrowsExceptionWithoutTemplate(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->twigAdapter);

        $this->expectException(InvalidArgumentException::class);

        $htmlTemplateEncoder->encode(data: [], format: HtmlTemplateEncoder::FORMAT);
    }

    public function testEncodeThrowsExceptionOnTwigAdapterError(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->twigAdapter);

        $this->twigAdapter
            ->expects($this->once())
            ->method(constraint: 'render')
            ->willThrowException(new TwigAdapterException());

        $this->expectException(RuntimeException::class);

        $htmlTemplateEncoder->encode(
            data: [],
            format: HtmlTemplateEncoder::FORMAT,
            context: [HtmlTemplateEncoder::TEMPLATE => 'example.html.twig'],
        );
    }

    public function testSupportsEncoding(): void
    {
        $htmlTemplateEncoder = new HtmlTemplateEncoder($this->twigAdapter);

        $this->assertTrue($htmlTemplateEncoder->supportsEncoding(format: HtmlTemplateEncoder::FORMAT));
        $this->assertFalse($htmlTemplateEncoder->supportsEncoding(format: ''));
    }
}
