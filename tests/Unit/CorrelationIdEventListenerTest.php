<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\HttpKernel\EventListener\CorrelationIdEventListener;
use App\Infrastructure\Observability\CorrelationIdProvider;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class CorrelationIdEventListenerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->httpKernel = $this->createMock(type: HttpKernelInterface::class);
    }

    public function testOnResponseEventWithNotMainRequest(): void
    {
        $correlationIdEventListener = new CorrelationIdEventListener(
            correlationIdProvider: new CorrelationIdProvider(),
        );

        $responseEvent = new ResponseEvent(
            kernel: $this->httpKernel,
            request: new Request(),
            requestType: HttpKernelInterface::SUB_REQUEST,
            response: new Response(),
        );

        $correlationIdEventListener->onResponse($responseEvent);

        $this->expectNotToPerformAssertions();
    }
}
