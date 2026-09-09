<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\HttpKernel\EventListener\RequestPayloadEventListener;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class RequestPayloadEventListenerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->httpKernel = $this->createMock(type: HttpKernelInterface::class);
        $this->normalizer = $this->createMock(type: NormalizerInterface::class);
        $this->requestPayloadEventListener = new RequestPayloadEventListener($this->normalizer);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testIgnoresSubRequest(): void
    {
        $request = new Request();

        $this->normalizer
            ->expects($this->never())
            ->method(constraint: 'normalize');

        $requestEvent = new RequestEvent(
            kernel: $this->httpKernel,
            request: $request,
            requestType: HttpKernelInterface::SUB_REQUEST,
        );

        ($this->requestPayloadEventListener)($requestEvent);

        self::assertFalse($request->attributes->has(key: '_payload'));
    }
}
