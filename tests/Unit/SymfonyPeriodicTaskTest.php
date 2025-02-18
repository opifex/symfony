<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Presentation\Scheduler\SymfonyPeriodicTask;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

final class SymfonyPeriodicTaskTest extends Unit
{
    private LoggerInterface&MockObject $logger;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->logger = $this->createMock(originalClassName: LoggerInterface::class);
    }

    public function testInvokePeriodicTask(): void
    {
        new SymfonyPeriodicTask($this->logger)();
    }
}
