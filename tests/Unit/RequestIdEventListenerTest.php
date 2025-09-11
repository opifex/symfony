<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\EventListener\RequestIdEventListener;
use App\Domain\Contract\Identification\RequestIdGeneratorInterface;
use App\Domain\Contract\Identification\RequestIdStorageInterface;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class RequestIdEventListenerTest extends TestCase
{
    private HttpKernelInterface&MockObject $httpKernel;

    private InputInterface&MockObject $input;

    private OutputInterface&MockObject $output;

    private RequestIdGeneratorInterface&MockObject $requestIdGenerator;

    private RequestIdStorageInterface&MockObject $requestIdStorage;

    #[Override]
    protected function setUp(): void
    {
        $this->httpKernel = $this->createMock(type: HttpKernelInterface::class);
        $this->input = $this->createMock(type: InputInterface::class);
        $this->output = $this->createMock(type: OutputInterface::class);
        $this->requestIdGenerator = $this->createMock(type: RequestIdGeneratorInterface::class);
        $this->requestIdStorage = $this->createMock(type: RequestIdStorageInterface::class);
    }

    public function testOnConsoleCommand(): void
    {
        $requestIdEventListener = new RequestIdEventListener(
            requestIdGenerator: $this->requestIdGenerator,
            requestIdStorage: $this->requestIdStorage,
        );

        $consoleCommandEvent = new ConsoleCommandEvent(
            command: new Command(),
            input: $this->input,
            output: $this->output,
        );

        $this->expectNotToPerformAssertions();

        $requestIdEventListener->onConsoleCommand($consoleCommandEvent);
    }

    public function testOnConsoleCommandWithEmptyCommand(): void
    {
        $requestIdEventListener = new RequestIdEventListener(
            requestIdGenerator: $this->requestIdGenerator,
            requestIdStorage: $this->requestIdStorage,
        );

        $consoleCommandEvent = new ConsoleCommandEvent(
            command: null,
            input: $this->input,
            output: $this->output,
        );

        $this->expectNotToPerformAssertions();

        $requestIdEventListener->onConsoleCommand($consoleCommandEvent);
    }

    public function testOnRequestEventWithNotMainRequest(): void
    {
        $requestIdEventListener = new RequestIdEventListener(
            requestIdGenerator: $this->requestIdGenerator,
            requestIdStorage: $this->requestIdStorage,
        );

        $requestEvent = new RequestEvent(
            kernel: $this->httpKernel,
            request: new Request(),
            requestType: HttpKernelInterface::SUB_REQUEST,
        );

        $this->expectNotToPerformAssertions();

        $requestIdEventListener->onRequest($requestEvent);
    }

    public function testOnResponseEventWithNotMainRequest(): void
    {
        $requestIdEventListener = new RequestIdEventListener(
            requestIdGenerator: $this->requestIdGenerator,
            requestIdStorage: $this->requestIdStorage,
        );

        $responseEvent = new ResponseEvent(
            kernel: $this->httpKernel,
            request: new Request(),
            requestType: HttpKernelInterface::SUB_REQUEST,
            response: new Response(),
        );

        $this->expectNotToPerformAssertions();

        $requestIdEventListener->onResponse($responseEvent);
    }
}
