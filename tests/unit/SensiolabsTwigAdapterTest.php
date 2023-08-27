<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Exception\TwigAdapterException;
use App\Infrastructure\Adapter\SensiolabsTwigAdapter;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Twig\Environment;
use Twig\Error\Error;

final class SensiolabsTwigAdapterTest extends Unit
{
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->environment = $this->createMock(originalClassName: Environment::class);
    }

    /**
     * @throws TwigAdapterException
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

        $this->expectException(TwigAdapterException::class);

        $sensiolabsTwigAdapter->render(name: 'example.html.twig');
    }
}
