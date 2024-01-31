<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Exception\TemplateEngineException;
use App\Infrastructure\Adapter\SensiolabsTwigAdapter;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Twig\Environment;
use Twig\Error\Error;

final class SensiolabsTwigAdapterTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->environment = $this->createMock(originalClassName: Environment::class);
    }

    /**
     * @throws TemplateEngineException
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

        $this->expectException(TemplateEngineException::class);

        $sensiolabsTwigAdapter->render(name: 'example.html.twig');
    }
}
