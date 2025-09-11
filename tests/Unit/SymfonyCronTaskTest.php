<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Presentation\Scheduler\SymfonyCronTask;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class SymfonyCronTaskTest extends TestCase
{
    private LoggerInterface&MockObject $logger;

    #[Override]
    protected function setUp(): void
    {
        $this->logger = $this->createMock(type: LoggerInterface::class);
    }

    public function testInvokeCronTask(): void
    {
        $this->expectNotToPerformAssertions();

        new SymfonyCronTask($this->logger)();
    }
}
