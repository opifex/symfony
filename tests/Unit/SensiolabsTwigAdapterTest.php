<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Adapter\Sensiolabs\Exception\RenderingFailedException;
use App\Infrastructure\Adapter\Sensiolabs\TwigTemplateRenderer;
use Override;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\Error;

#[AllowDynamicProperties]
final class SensiolabsTwigAdapterTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->environment = $this->createMock(type: Environment::class);
    }

    public function testRenderExistedTemplate(): void
    {
        $sensiolabsTwigAdapter = new TwigTemplateRenderer($this->environment);
        $content = 'content';

        $this->environment
            ->expects($this->once())
            ->method(constraint: 'render')
            ->willReturn($content);

        $rendered = $sensiolabsTwigAdapter->render(name: 'example.html.twig');

        $this->assertSame($content, $rendered);
    }

    public function testRenderThrowsExceptionOnTwigError(): void
    {
        $sensiolabsTwigAdapter = new TwigTemplateRenderer($this->environment);

        $this->environment
            ->expects($this->once())
            ->method(constraint: 'render')
            ->willThrowException(new Error(message: ''));

        $this->expectException(RenderingFailedException::class);

        $sensiolabsTwigAdapter->render(name: 'example.html.twig');
    }
}
