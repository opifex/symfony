<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Presentation\Controller\AbstractController;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class AbstractControllerTest extends Unit
{
    private MessageBusInterface&MockObject $messageBus;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(type: MessageBusInterface::class);
    }

    public function testInvokeThrowsExceptionOnMultipleHandlers(): void
    {
        $controller = new class ($this->messageBus) extends AbstractController {
            public function __invoke(object $message): Response
            {
                return $this->getHandledResult($message);
            }
        };

        $message = new stdClass();
        $handledStamp1 = new HandledStamp(result: null, handlerName: 'handler1');
        $handledStamp2 = new HandledStamp(result: null, handlerName: 'handler2');
        $envelope = new Envelope(message: $message, stamps: [$handledStamp1, $handledStamp2]);

        $this->messageBus
            ->expects($this->once())
            ->method(constraint: 'dispatch')
            ->with($message)
            ->willReturn($envelope);

        $this->expectException(LogicException::class);

        ($controller)($message);
    }

    public function testInvokeThrowsExceptionOnInvalidResult(): void
    {
        $controller = new class ($this->messageBus) extends AbstractController {
            public function __invoke(object $message): Response
            {
                return $this->getHandledResult($message);
            }
        };

        $message = new stdClass();
        $handledStamp = new HandledStamp(result: null, handlerName: 'handler');
        $envelope = new Envelope(message: $message, stamps: [$handledStamp]);

        $this->messageBus
            ->expects($this->once())
            ->method(constraint: 'dispatch')
            ->with($message)
            ->willReturn($envelope);

        $this->expectException(LogicException::class);

        ($controller)($message);
    }
}
