<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Service\RequestTraceManager;
use PHPUnit\Framework\TestCase;

final class RequestTraceManagerTest extends TestCase
{
    public function testInMemoryStorage(): void
    {
        $requestTraceManager = new RequestTraceManager();

        $correlationId = '00000000-0000-6000-8000-000000000000';
        $requestTraceManager->setCorrelationId($correlationId);

        $this->assertEquals($correlationId, $requestTraceManager->getCorrelationId());

        $requestTraceManager->reset();

        $this->assertNotSame(expected: $correlationId, actual: $requestTraceManager->getCorrelationId());
    }
}
