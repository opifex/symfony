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
        $this->htmlTemplateEncoder = new HtmlTemplateEncoder($this->twigAdapter);
    }

    public function testEncodeWithExistedTemplate(): void
    {
        $content = 'content';
        $this->twigAdapter
            ->expects($this->once())
            ->method(constraint: 'render')
            ->willReturn($content);

        $encoded = $this->htmlTemplateEncoder->encode(
            data: [],
            format: HtmlTemplateEncoder::FORMAT,
            context: [HtmlTemplateEncoder::TEMPLATE => 'example.html.twig'],
        );

        $this->assertEquals($content, $encoded);
    }

    public function testEncodeThrowsExceptionWithInvalidData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->htmlTemplateEncoder->encode(
            data: new stdClass(),
            format: HtmlTemplateEncoder::FORMAT,
            context: [HtmlTemplateEncoder::TEMPLATE => 'example.html.twig'],
        );
    }

    public function testEncodeThrowsExceptionWithoutTemplate(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->htmlTemplateEncoder->encode(data: [], format: HtmlTemplateEncoder::FORMAT);
    }

    public function testEncodeThrowsExceptionOnTwigAdapterError(): void
    {
        $this->twigAdapter
            ->expects($this->once())
            ->method(constraint: 'render')
            ->willThrowException(new TwigAdapterException());

        $this->expectException(RuntimeException::class);

        $this->htmlTemplateEncoder->encode(
            data: [],
            format: HtmlTemplateEncoder::FORMAT,
            context: [HtmlTemplateEncoder::TEMPLATE => 'example.html.twig'],
        );
    }

    public function testSupportsEncoding(): void
    {
        $this->assertTrue($this->htmlTemplateEncoder->supportsEncoding(format: HtmlTemplateEncoder::FORMAT));
        $this->assertFalse($this->htmlTemplateEncoder->supportsEncoding(format: ''));
    }
}
