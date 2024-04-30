<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Listener\RequestIdEventListener;
use App\Domain\Contract\RequestIdGeneratorInterface;
use App\Domain\Contract\RequestIdStorageInterface;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class RequestIdEventListenerTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->requestIdGenerator = $this->createMock(originalClassName: RequestIdGeneratorInterface::class);
        $this->requestIdStorage = $this->createMock(originalClassName: RequestIdStorageInterface::class);
        $this->httpKernel = $this->createMock(originalClassName: HttpKernelInterface::class);
    }

    public function testOnRequestEventNotForMainRequest(): void
    {
        $requestIdEventListener = new RequestIdEventListener(
            requestIdGenerator: $this->requestIdGenerator,
            requestIdStorage: $this->requestIdStorage,
        );

        $requestEvent = (new RequestEvent(
            kernel: $this->httpKernel,
            request: new Request(),
            requestType: HttpKernelInterface::SUB_REQUEST,
        ));

        $requestIdEventListener->onRequest($requestEvent);
    }

    public function testOnResponseEventNotForMainRequest(): void
    {
        $requestIdEventListener = new RequestIdEventListener(
            requestIdGenerator: $this->requestIdGenerator,
            requestIdStorage: $this->requestIdStorage,
        );

        $responseEvent = (new ResponseEvent(
            kernel: $this->httpKernel,
            request: new Request(),
            requestType: HttpKernelInterface::SUB_REQUEST,
            response: new Response(),
        ));

        $requestIdEventListener->onResponse($responseEvent);
    }
}
