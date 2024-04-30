<?php

declare(strict_types=1);

namespace App\Tests;

use App\Presentation\Scheduler\SymfonyPeriodicTask;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Log\LoggerInterface;

final class SymfonyPeriodicTaskTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->logger = $this->createMock(originalClassName: LoggerInterface::class);
    }

    public function testInvokePeriodicTask(): void
    {
        (new SymfonyPeriodicTask($this->logger))();
    }
}
