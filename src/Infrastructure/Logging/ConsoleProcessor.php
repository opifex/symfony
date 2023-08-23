<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsMonologProcessor]
final class ConsoleProcessor
{
    /** @var array&array<string, mixed> */
    private array $cache = [];

    public function __invoke(LogRecord $record): LogRecord
    {
        if ($this->cache !== []) {
            $record->extra['console'] = $this->cache;
        }

        return $record;
    }

    #[AsEventListener(event: ConsoleCommandEvent::class, priority: 128)]
    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $this->cache = [
            'command' => $event->getCommand()?->getName(),
            'arguments' => $event->getInput()->getArguments(),
            'options' => array_filter($event->getInput()->getOptions()),
        ];
    }
}
