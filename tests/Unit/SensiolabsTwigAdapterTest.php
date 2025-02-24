<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Exception\TemplateRendererException;
use App\Infrastructure\Adapter\SensiolabsTwigAdapter;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Environment;
use Twig\Error\Error;

final class SensiolabsTwigAdapterTest extends Unit
{
    private Environment&MockObject $environment;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->environment = $this->createMock(type: Environment::class);
    }

    /**
     * @throws TemplateRendererException
     */
    public function testRenderExistedTemplate(): void
    {
        $sensiolabsTwigAdapter = new SensiolabsTwigAdapter($this->environment);
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
        $sensiolabsTwigAdapter = new SensiolabsTwigAdapter($this->environment);

        $this->environment
            ->expects($this->once())
            ->method(constraint: 'render')
            ->willThrowException(new Error(message: ''));

        $this->expectException(TemplateRendererException::class);

        $sensiolabsTwigAdapter->render(name: 'example.html.twig');
    }
}
