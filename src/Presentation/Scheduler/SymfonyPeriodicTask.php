<?php

declare(strict_types=1);

namespace App\Presentation\Scheduler;

use Psr\Log\LoggerInterface;
use Symfony\Component\Scheduler\Attribute\AsPeriodicTask;

#[AsPeriodicTask(frequency: '60 minutes')]
final class SymfonyPeriodicTask
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(): void
    {
        $this->logger->info('Symfony periodic task processed.');
    }
}
