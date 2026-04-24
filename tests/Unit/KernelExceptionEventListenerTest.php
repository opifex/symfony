<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\HttpKernel\EventListener\KernelExceptionEventListener;
use LogicException;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class KernelExceptionEventListenerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->kernel = $this->createMock(type: KernelInterface::class);
        $this->logger = $this->createMock(type: LoggerInterface::class);
        $this->normalizer = $this->createMock(type: NormalizerInterface::class);
    }

    /**
     * @throws ExceptionInterface
     * @throws ReflectionException
     */
    public function testInvokeWithLogicException(): void
    {
        $event = new ExceptionEvent(
            kernel: $this->kernel,
            request: new Request(),
            requestType: HttpKernelInterface::MAIN_REQUEST,
            e: new LogicException(),
        );

        new KernelExceptionEventListener($this->logger, $this->normalizer)($event);

        $this->expectNotToPerformAssertions();
    }
}
