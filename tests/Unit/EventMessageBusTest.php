<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Messenger\MessageBus\EventMessageBus;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class EventMessageBusTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(type: MessageBusInterface::class);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testPublishIgnoresMissingHandlers(): void
    {
        $event = new stdClass();
        $eventMessageBus = new EventMessageBus($this->messageBus);

        $this->messageBus
            ->expects($this->once())
            ->method(constraint: 'dispatch')
            ->with($event)
            ->willThrowException(new NoHandlerForMessageException());

        $eventMessageBus->publish($event);
    }
}
