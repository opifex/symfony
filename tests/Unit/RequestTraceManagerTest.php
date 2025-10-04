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

        $traceId = '00000000-0000-6000-8000-000000000000';
        $requestTraceManager->setTraceId($traceId);

        $this->assertEquals($traceId, $requestTraceManager->getTraceId());

        $requestTraceManager->reset();

        $this->assertNotSame(expected: $traceId, actual: $requestTraceManager->getTraceId());
    }
}
