<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Contract\RequestIdGeneratorInterface;
use App\Application\Contract\RequestIdStorageInterface;
use App\Application\EventListener\RequestIdEventListener;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Scheduler\Event\PreRunEvent;
use Symfony\Component\Scheduler\Generator\MessageContext;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Component\Scheduler\Trigger\TriggerInterface;

final class RequestIdEventListenerTest extends TestCase
{
    private HttpKernelInterface&MockObject $httpKernel;

    private InputInterface&MockObject $input;

    private OutputInterface&MockObject $output;

    private RequestIdGeneratorInterface&MockObject $requestIdGenerator;

    private RequestIdStorageInterface&MockObject $requestIdStorage;

    private ScheduleProviderInterface&MockObject $scheduleProvider;

    private TriggerInterface&MockObject $trigger;

    #[Override]
    protected function setUp(): void
    {
        $this->httpKernel = $this->createMock(type: HttpKernelInterface::class);
        $this->input = $this->createMock(type: InputInterface::class);
        $this->output = $this->createMock(type: OutputInterface::class);
        $this->requestIdGenerator = $this->createMock(type: RequestIdGeneratorInterface::class);
        $this->requestIdStorage = $this->createMock(type: RequestIdStorageInterface::class);
        $this->scheduleProvider = $this->createMock(type: ScheduleProviderInterface::class);
        $this->trigger = $this->createMock(type: TriggerInterface::class);
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

        $requestIdEventListener->onConsoleCommand($consoleCommandEvent);

        $this->expectNotToPerformAssertions();
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

        $requestIdEventListener->onConsoleCommand($consoleCommandEvent);

        $this->expectNotToPerformAssertions();
    }

    public function testOnPreRunEvent(): void
    {
        $requestIdEventListener = new RequestIdEventListener(
            requestIdGenerator: $this->requestIdGenerator,
            requestIdStorage: $this->requestIdStorage,
        );

        $preRunEvent = new PreRunEvent(
            schedule: $this->scheduleProvider,
            messageContext: new MessageContext(
                name: 'Test scheduler event',
                id: '00000000-0000-6000-8000-000000000000',
                trigger: $this->trigger,
                triggeredAt: new DatePoint(),
            ),
            message: new stdClass(),
        );

        $requestIdEventListener->onPreRunEvent($preRunEvent);

        $this->expectNotToPerformAssertions();
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

        $requestIdEventListener->onRequest($requestEvent);

        $this->expectNotToPerformAssertions();
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

        $requestIdEventListener->onResponse($responseEvent);

        $this->expectNotToPerformAssertions();
    }
}
