<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\EventListener\KernelExceptionEventListener;
use App\Domain\Contract\Protection\PrivacyDataProtectorInterface;
use LogicException;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class KernelExceptionEventListenerTest extends TestCase
{
    private KernelInterface&MockObject $kernel;

    private LoggerInterface&MockObject $logger;

    private NormalizerInterface&MockObject $normalizer;

    private PrivacyDataProtectorInterface&MockObject $privacyDataProtector;

    #[Override]
    protected function setUp(): void
    {
        $this->kernel = $this->createMock(type: KernelInterface::class);
        $this->logger = $this->createMock(type: LoggerInterface::class);
        $this->normalizer = $this->createMock(type: NormalizerInterface::class);
        $this->privacyDataProtector = $this->createMock(type: PrivacyDataProtectorInterface::class);
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

        $this->expectNotToPerformAssertions();

        new KernelExceptionEventListener($this->logger, $this->normalizer, $this->privacyDataProtector)($event);
    }
}
