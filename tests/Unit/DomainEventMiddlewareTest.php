<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Messenger\DomainEventCollector;
use App\Infrastructure\Messenger\MessageBus\EventMessageBus;
use App\Infrastructure\Messenger\Middleware\DomainEventMiddleware;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class DomainEventMiddlewareTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->nextMiddleware = $this->createMock(type: MiddlewareInterface::class);
        $this->stack = $this->createMock(type: StackInterface::class);
        $this->domainEventCollector = new DomainEventCollector();
        $this->messageBus = $this->createMock(type: MessageBusInterface::class);
        $this->eventMessageBus = new EventMessageBus($this->messageBus);
    }

    /**
     * @throws Throwable
     */
    public function testPublishesEventsRecordedWhileHandlingTheEnvelope(): void
    {
        $envelope = new Envelope(new stdClass());
        $event = new stdClass();

        $this->stack->method(constraint: 'next')->willReturn($this->nextMiddleware);
        $this->nextMiddleware
            ->expects($this->once())
            ->method(constraint: 'handle')
            ->with($envelope, $this->stack)
            ->willReturnCallback(function () use ($envelope, $event) {
                $this->domainEventCollector->collect($event);

                return $envelope;
            });

        $this->messageBus
            ->expects($this->once())
            ->method(constraint: 'dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));

        $publishDomainEventMiddleware = new DomainEventMiddleware(
            $this->domainEventCollector,
            $this->eventMessageBus,
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
        $firstEvent = new stdClass();
        $secondEvent = new stdClass();

        $this->stack->method(constraint: 'next')->willReturn($this->nextMiddleware);
        $this->nextMiddleware
            ->method(constraint: 'handle')
            ->willReturnCallback(function () use ($envelope, $firstEvent, $secondEvent) {
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

        $publishDomainEventMiddleware = new DomainEventMiddleware(
            $this->domainEventCollector,
            $this->eventMessageBus,
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
            ->willReturnCallback(function () {
                $this->domainEventCollector->collect(new stdClass());

                throw new RuntimeException(message: 'handler failed');
            });

        $this->messageBus->expects($this->never())->method(constraint: 'dispatch');

        $publishDomainEventMiddleware = new DomainEventMiddleware(
            $this->domainEventCollector,
            $this->eventMessageBus,
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
