<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Messenger\LocaleStamp;
use App\Infrastructure\Middleware\MessengerLocaleMiddleware;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Translation\LocaleSwitcher;

class LocaleMiddlewareTest extends Unit
{
    private MessengerLocaleMiddleware $localeMiddleware;

    private LocaleSwitcher&MockObject $localeSwitcher;

    private MiddlewareInterface&MockObject $nextMiddleware;

    private StackInterface&MockObject $stack;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->localeSwitcher = $this->createMock(originalClassName: LocaleSwitcher::class);
        $this->nextMiddleware = $this->createMock(originalClassName: MiddlewareInterface::class);
        $this->stack = $this->createMock(originalClassName: StackInterface::class);
        $this->localeMiddleware = new MessengerLocaleMiddleware($this->localeSwitcher);
    }

    public function testHandleWithLocaleStamp(): void
    {
        $envelope = new Envelope(new stdClass(), [new LocaleStamp(locale: 'uk')]);

        $this->nextMiddleware
            ->expects($this->once())
            ->method(constraint: 'handle')
            ->willReturn($envelope);

        $this->stack
            ->expects($this->once())
            ->method(constraint: 'next')
            ->willReturn($this->nextMiddleware);

        $this->localeSwitcher
            ->expects($this->once())
            ->method(constraint: 'getLocale')
            ->willReturn(value: 'en');

        $envelope = $this->localeMiddleware->handle($envelope, $this->stack);

        $localeStamp = $envelope->last(stampFqcn: LocaleStamp::class);

        $this->assertInstanceOf(expected: LocaleStamp::class, actual: $localeStamp);
        $this->assertEquals(expected: 'uk', actual: $localeStamp->getLocale());
    }
}
