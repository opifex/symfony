<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Contract\FailedMessageCounterInterface;
use App\Presentation\Scheduler\FailedMessagesMonitoringTask;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class FailedMessagesMonitoringTaskTest extends TestCase
{
    public function testLogsErrorWhenFailedMessagesExist(): void
    {
        $failedMessageCounter = $this->createStub(type: FailedMessageCounterInterface::class);
        $failedMessageCounter->method(constraint: 'count')->willReturn(value: 3);
        $logger = $this->createMock(type: LoggerInterface::class);
        $logger->expects($this->once())->method(constraint: 'error');

        new FailedMessagesMonitoringTask($failedMessageCounter, $logger)();
    }

    public function testLogsNothingWithoutFailedMessages(): void
    {
        $failedMessageCounter = $this->createStub(type: FailedMessageCounterInterface::class);
        $failedMessageCounter->method(constraint: 'count')->willReturn(value: 0);
        $logger = $this->createMock(type: LoggerInterface::class);
        $logger->expects($this->never())->method(constraint: 'error');

        new FailedMessagesMonitoringTask($failedMessageCounter, $logger)();
    }
}
