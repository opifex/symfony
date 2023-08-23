<?php

declare(strict_types=1);

namespace App\Application\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: ConsoleCommandEvent::class)]
final class ConsoleEventListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(ConsoleCommandEvent $event): void
    {
        $this->logger->info('Application console event.', [
            'command' => $event->getCommand()?->getName(),
        ]);
    }
}
