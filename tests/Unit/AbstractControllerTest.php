<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Presentation\Controller\AbstractController;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class AbstractControllerTest extends Unit
{
    private MessageBusInterface&MockObject $messageBus;
    private NormalizerInterface&MockObject $normalizer;

    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(originalClassName: MessageBusInterface::class);
        $this->normalizer = $this->createMock(originalClassName: NormalizerInterface::class);
    }

    /**
     * @throws Exception
     */
    public function testHandleThrowsExceptionOnMultipleHandlers(): void
    {
        $controller = new class ($this->messageBus, $this->normalizer) extends AbstractController {
            public function testHandle(object $message): mixed
            {
                return $this->handle($message);
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

        $controller->testHandle($message);
    }
}
