<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Domain\Foundation\Event\EventInterface;
use App\Infrastructure\Messenger\DomainEventCollector;
use App\Infrastructure\Messenger\Middleware\EventDispatcherMiddleware;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Throwable;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class EventDispatcherMiddlewareTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->nextMiddleware = $this->createMock(type: MiddlewareInterface::class);
        $this->stack = $this->createMock(type: StackInterface::class);
        $this->domainEventCollector = new DomainEventCollector();
        $this->messageBus = $this->createMock(type: MessageBusInterface::class);
    }

    /**
     * @throws Throwable
     */
    public function testPublishesEventsRecordedWhileHandlingTheEnvelope(): void
    {
        $envelope = new Envelope(new stdClass());
        $event = new class implements EventInterface {};

        $this->stack->method(constraint: 'next')->willReturn($this->nextMiddleware);
        $this->nextMiddleware
            ->expects($this->once())
            ->method(constraint: 'handle')
            ->with($envelope, $this->stack)
            ->willReturnCallback(function () use ($envelope, $event): Envelope {
                $this->domainEventCollector->collect($event);

                return $envelope;
            });

        $this->messageBus
            ->expects($this->once())
            ->method(constraint: 'dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));

        $publishDomainEventMiddleware = new EventDispatcherMiddleware(
            $this->domainEventCollector,
            $this->messageBus,
        );

        $result = $publishDomainEventMiddleware->handle($envelope, $this->stack);

        self::assertSame($envelope, $result);
        self::assertSame([], $this->domainEventCollector->releaseEvents());
    }

    /**
     * @throws Throwable
     */
    public function testPublishesEachRecordedEventInASeparateCall(): void
    {
        $envelope = new Envelope(new stdClass());
        $firstEvent = new class implements EventInterface {};
        $secondEvent = new class implements EventInterface {};

        $this->stack->method(constraint: 'next')->willReturn($this->nextMiddleware);
        $this->nextMiddleware
            ->method(constraint: 'handle')
            ->willReturnCallback(function () use ($envelope, $firstEvent, $secondEvent): Envelope {
                $this->domainEventCollector->collect($firstEvent, $secondEvent);

                return $envelope;
            });

        $publishedEvents = [];
        $this->messageBus
            ->expects($this->exactly(count: 2))
            ->method(constraint: 'dispatch')
            ->willReturnCallback(function (object $event) use (&$publishedEvents): Envelope {
                $publishedEvents[] = $event;

                return new Envelope($event);
            });

        $publishDomainEventMiddleware = new EventDispatcherMiddleware(
            $this->domainEventCollector,
            $this->messageBus,
        );

        $publishDomainEventMiddleware->handle($envelope, $this->stack);

        self::assertSame([$firstEvent, $secondEvent], $publishedEvents);
    }

    /**
     * @throws Throwable
     */
    public function testDoesNotPublishEventsWhenHandlingTheEnvelopeFails(): void
    {
        $envelope = new Envelope(new stdClass());

        $this->stack->method(constraint: 'next')->willReturn($this->nextMiddleware);
        $this->nextMiddleware
            ->method(constraint: 'handle')
            ->willReturnCallback(function (): never {
                $this->domainEventCollector->collect(
                    new class implements EventInterface {},
                );

                throw new RuntimeException(message: 'handler failed');
            });

        $this->messageBus->expects($this->never())->method(constraint: 'dispatch');

        $publishDomainEventMiddleware = new EventDispatcherMiddleware(
            $this->domainEventCollector,
            $this->messageBus,
        );

        try {
            $publishDomainEventMiddleware->handle($envelope, $this->stack);
            self::fail('Expected RuntimeException was not thrown.');
        } catch (RuntimeException $runtimeException) {
            self::assertSame(expected: 'handler failed', actual: $runtimeException->getMessage());
        }

        self::assertSame([], $this->domainEventCollector->releaseEvents());
    }
}
