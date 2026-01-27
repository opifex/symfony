<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Messenger\MessageBus\QueryMessageBus;
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
final class QueryMessageBusTest extends TestCase
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
        $messageBus = new QueryMessageBus($this->messageBus);
        $queryMessage = new stdClass();

        $this->messageBus
            ->expects(self::once())
            ->method(constraint: 'dispatch')
            ->with($queryMessage)
            ->willReturn(new Envelope($queryMessage, $stamps));

        $this->expectException(LogicException::class);

        $messageBus->ask($queryMessage);
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
