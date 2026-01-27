<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Messenger\Middleware\CorrelationIdMiddleware;
use App\Infrastructure\Messenger\Stamp\CorrelationIdStamp;
use App\Infrastructure\Observability\CorrelationIdProvider;
use Override;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

#[AllowDynamicProperties]
final class CorrelationIdMiddlewareTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->middleware = $this->createMock(type: MiddlewareInterface::class);
        $this->stack = $this->createMock(type: StackInterface::class);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testHandleEnvelopeWithRequestIdStamp(): void
    {
        $correlationIdMiddleware = new CorrelationIdMiddleware(new CorrelationIdProvider());
        $correlationIdStamp = new CorrelationIdStamp(correlationId: '00000000-0000-6000-8000-000000000000');
        $envelope = new Envelope(new stdClass(), [$correlationIdStamp]);

        $this->stack
            ->expects($this->once())
            ->method(constraint: 'next')
            ->willReturn($this->middleware);

        $this->middleware
            ->expects($this->once())
            ->method(constraint: 'handle')
            ->with($envelope, $this->stack)
            ->willReturn($envelope);

        $correlationIdMiddleware->handle($envelope, $this->stack);
    }
}
