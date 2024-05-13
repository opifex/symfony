<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Contract\RequestIdStorageInterface;
use App\Infrastructure\Messenger\RequestIdMiddleware;
use App\Infrastructure\Messenger\RequestIdStamp;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class RequestIdMiddlewareTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->requestIdStorage = $this->createMock(originalClassName: RequestIdStorageInterface::class);
        $this->middleware = $this->createMock(originalClassName: MiddlewareInterface::class);
        $this->stack = $this->createMock(originalClassName: StackInterface::class);
    }

    public function testHandleEnvelopeWithRequestIdStamp(): void
    {
        $middleware = new RequestIdMiddleware($this->requestIdStorage);

        $requestIdStamp = new RequestIdStamp(requestId: '00000000-0000-6000-8000-000000000000');
        $envelope = new Envelope(new stdClass(), [$requestIdStamp]);

        $this->stack
            ->expects($this->once())
            ->method(constraint: 'next')
            ->willReturn($this->middleware);

        $this->middleware
            ->expects($this->once())
            ->method(constraint: 'handle')
            ->with($envelope, $this->stack)
            ->willReturn($envelope);

        $middleware->handle($envelope, $this->stack);
    }
}
