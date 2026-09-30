<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Foundation\Event\EventInterface;
use App\Infrastructure\Messenger\DomainEventCollector;
use PHPUnit\Framework\TestCase;

final class DomainEventCollectorTest extends TestCase
{
    public function testReleaseEventsReturnsEmptyArrayWhenNothingRecorded(): void
    {
        $domainEventCollector = new DomainEventCollector();

        self::assertSame(expected: [], actual: $domainEventCollector->releaseEvents());
    }

    public function testReleaseEventsReturnsRecordedEventsInOrder(): void
    {
        $domainEventCollector = new DomainEventCollector();
        $firstEvent = new class implements EventInterface {};
        $secondEvent = new class implements EventInterface {};

        $domainEventCollector->collect($firstEvent);
        $domainEventCollector->collect($secondEvent);

        self::assertSame(expected: [$firstEvent, $secondEvent], actual: $domainEventCollector->releaseEvents());
    }

    public function testReleaseEventsClearsTheBuffer(): void
    {
        $domainEventCollector = new DomainEventCollector();
        $domainEventCollector->collect(
            new class implements EventInterface {},
        );

        $domainEventCollector->releaseEvents();

        self::assertSame(expected: [], actual: $domainEventCollector->releaseEvents());
    }

    public function testResetClearsRecordedEventsWithoutPublishing(): void
    {
        $domainEventCollector = new DomainEventCollector();
        $domainEventCollector->collect(
            new class implements EventInterface {},
        );

        $domainEventCollector->reset();

        self::assertSame(expected: [], actual: $domainEventCollector->releaseEvents());
    }
}
