<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Messenger\MessageBus\CommandMessageBus;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AllowDynamicProperties]
final class CommandMessageBusTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(type: MessageBusInterface::class);
    }

    /**
     * @throws ExceptionInterface
     */
    #[DataProvider(methodName: 'handledStampsProvider')]
    public function testInvokeThrowsLogicExceptionOnInvalidHandledResult(array $stamps): void
    {
        $messageBus = new CommandMessageBus($this->messageBus);
        $commandMessage = new stdClass();

        $this->messageBus
            ->expects(self::once())
            ->method(constraint: 'dispatch')
            ->with($commandMessage)
            ->willReturn(new Envelope($commandMessage, $stamps));

        $this->expectException(LogicException::class);

        $messageBus->dispatch($commandMessage);
    }

    public static function handledStampsProvider(): iterable
    {
        yield 'multiple stamps for envelope' => [
            [
                new HandledStamp(result: null, handlerName: 'handler1'),
                new HandledStamp(result: null, handlerName: 'handler2'),
            ],
        ];
    }
}
