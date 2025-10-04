<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Contract\RequestTraceManagerInterface;
use App\Application\EventListener\RequestTraceEventListener;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class RequestTraceEventListenerTest extends TestCase
{
    private HttpKernelInterface&MockObject $httpKernel;

    private RequestTraceManagerInterface&MockObject $requestTraceManager;

    #[Override]
    protected function setUp(): void
    {
        $this->httpKernel = $this->createMock(type: HttpKernelInterface::class);
        $this->requestTraceManager = $this->createMock(type: RequestTraceManagerInterface::class);
    }

    public function testOnResponseEventWithNotMainRequest(): void
    {
        $requestTraceEventListener = new RequestTraceEventListener(
            requestTraceManager: $this->requestTraceManager,
        );

        $responseEvent = new ResponseEvent(
            kernel: $this->httpKernel,
            request: new Request(),
            requestType: HttpKernelInterface::SUB_REQUEST,
            response: new Response(),
        );

        $requestTraceEventListener->onResponse($responseEvent);

        $this->expectNotToPerformAssertions();
    }
}
