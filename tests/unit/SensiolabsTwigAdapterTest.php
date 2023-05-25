<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Exception\TwigAdapterException;
use App\Infrastructure\Adapter\SensiolabsTwigAdapter;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Environment;
use Twig\Error\Error;

final class SensiolabsTwigAdapterTest extends Unit
{
    private Environment&MockObject $environment;

    private SensiolabsTwigAdapter $sensiolabsTwigAdapter;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->environment = $this->createMock(originalClassName: Environment::class);
        $this->sensiolabsTwigAdapter = new SensiolabsTwigAdapter($this->environment);
    }

    /**
     * @throws TwigAdapterException
     */
    public function testRenderExistedTemplate(): void
    {
        $content = 'content';
        $this->environment
            ->expects($this->once())
            ->method(constraint: 'render')
            ->willReturn($content);

        $rendered = $this->sensiolabsTwigAdapter->render(name: 'example.html.twig');

        $this->assertEquals($content, $rendered);
    }

    public function testRenderThrowsExceptionOnTwigError(): void
    {
        $this->environment
            ->expects($this->once())
            ->method(constraint: 'render')
            ->willThrowException(new Error(message: ''));

        $this->expectException(TwigAdapterException::class);

        $this->sensiolabsTwigAdapter->render(name: 'example.html.twig');
    }
}
