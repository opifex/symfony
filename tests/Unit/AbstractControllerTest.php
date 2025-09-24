<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Presentation\Controller\AbstractController;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class AbstractControllerTest extends TestCase
{
    private MessageBusInterface&MockObject $messageBus;

    #[Override]
    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(type: MessageBusInterface::class);
    }

    #[DataProvider(methodName: 'handledStampsProvider')]
    public function testInvokeThrowsLogicExceptionOnInvalidHandledResult(array $stamps): void
    {
        $controller = new class ($this->messageBus) extends AbstractController {
            public function __invoke(object $request): Response
            {
                return $this->getHandledResult($request);
            }
        };

        $request = new stdClass();
        $envelope = new Envelope($request, $stamps);

        $this->messageBus
            ->expects(self::once())
            ->method(constraint: 'dispatch')
            ->with($request)
            ->willReturn($envelope);

        $this->expectException(LogicException::class);

        $controller($request);
    }

    public static function handledStampsProvider(): iterable
    {
        yield 'single stamp with invalid result' => [
            [
                new HandledStamp(result: null, handlerName: 'handler'),
            ],
        ];
        yield 'multiple stamps for envelope' => [
            [
                new HandledStamp(result: null, handlerName: 'handler1'),
                new HandledStamp(result: null, handlerName: 'handler2'),
            ],
        ];
    }
}
