<?php

declare(strict_types=1);

namespace App\Presentation\Scheduler;

use Psr\Log\LoggerInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('@hourly')]
final class SymfonyPeriodicTask
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(): void
    {
        $this->logger->info('Symfony periodic task processed.');
    }
}
