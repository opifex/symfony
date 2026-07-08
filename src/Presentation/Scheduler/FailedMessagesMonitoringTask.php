<?php

declare(strict_types=1);

namespace App\Presentation\Scheduler;

use App\Application\Contract\FailedMessageCounterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('*/15 * * * *', schedule: 'tasks')]
final readonly class FailedMessagesMonitoringTask
{
    public function __construct(
        private FailedMessageCounterInterface $failedMessageCounter,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(): void
    {
        $failedMessageCount = $this->failedMessageCounter->count();

        if ($failedMessageCount > 0) {
            $this->logger->error(
                message: 'Messenger failed transport contains unprocessed messages.',
                context: ['count' => $failedMessageCount],
            );
        }
    }
}
