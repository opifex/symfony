<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Contract\RequestTraceManagerInterface;
use App\Infrastructure\Messenger\Middleware\RequestTraceMiddleware;
use App\Infrastructure\Messenger\Stamp\RequestTraceStamp;
use Override;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class RequestTraceMiddlewareTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->middleware = $this->createMock(type: MiddlewareInterface::class);
        $this->requestTraceManager = $this->createMock(type: RequestTraceManagerInterface::class);
        $this->stack = $this->createMock(type: StackInterface::class);
    }

    public function testHandleEnvelopeWithRequestIdStamp(): void
    {
        $requestTraceMiddleware = new RequestTraceMiddleware($this->requestTraceManager);
        $requestTraceStamp = new RequestTraceStamp(correlationId: '00000000-0000-6000-8000-000000000000');
        $envelope = new Envelope(new stdClass(), [$requestTraceStamp]);

        $this->stack
            ->expects($this->once())
            ->method(constraint: 'next')
            ->willReturn($this->middleware);

        $this->middleware
            ->expects($this->once())
            ->method(constraint: 'handle')
            ->with($envelope, $this->stack)
            ->willReturn($envelope);

        $requestTraceMiddleware->handle($envelope, $this->stack);
    }
}
