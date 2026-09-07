<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Messenger\DomainEventCollector;
use PHPUnit\Framework\TestCase;
use stdClass;

final class DomainEventCollectorTest extends TestCase
{
    public function testReleaseAllReturnsEmptyArrayWhenNothingRecorded(): void
    {
        $domainEventCollector = new DomainEventCollector();

        self::assertSame([], $domainEventCollector->releaseEvents());
    }

    public function testReleaseAllReturnsRecordedEventsInOrder(): void
    {
        $domainEventCollector = new DomainEventCollector();
        $firstEvent = new stdClass();
        $secondEvent = new stdClass();

        $domainEventCollector->collect($firstEvent);
        $domainEventCollector->collect($secondEvent);

        self::assertSame([$firstEvent, $secondEvent], $domainEventCollector->releaseEvents());
    }

    public function testReleaseAllClearsTheBuffer(): void
    {
        $domainEventCollector = new DomainEventCollector();
        $domainEventCollector->collect(new stdClass());

        $domainEventCollector->releaseEvents();

        self::assertSame([], $domainEventCollector->releaseEvents());
    }

    public function testResetClearsRecordedEventsWithoutPublishing(): void
    {
        $domainEventCollector = new DomainEventCollector();
        $domainEventCollector->collect(new stdClass());

        $domainEventCollector->reset();

        self::assertSame([], $domainEventCollector->releaseEvents());
    }
}
