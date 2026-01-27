<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Observability\CorrelationIdProvider;
use PHPUnit\Framework\TestCase;

#[AllowDynamicProperties]
final class CorrelationIdProviderTest extends TestCase
{
    public function testInMemoryStorage(): void
    {
        $correlationIdProvider = new CorrelationIdProvider();

        $correlationId = '00000000-0000-6000-8000-000000000000';
        $correlationIdProvider->setCorrelationId($correlationId);

        $this->assertEquals($correlationId, $correlationIdProvider->getCorrelationId());

        $correlationIdProvider->reset();

        $this->assertNotSame(expected: $correlationId, actual: $correlationIdProvider->getCorrelationId());
    }
}
