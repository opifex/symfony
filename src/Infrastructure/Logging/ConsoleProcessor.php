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
    private static array $cache = [];

    public function __invoke(LogRecord $record): LogRecord
    {
        if (self::$cache !== []) {
            $record->extra['console'] = self::$cache;
        }

        return $record;
    }

    #[AsEventListener(event: ConsoleCommandEvent::class, priority: 128)]
    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $callback = fn(mixed $value, string $key) => !empty($value) && !in_array($key, ['command', 'env']);
        $filterParams = fn(array $params) => array_filter($params, $callback, mode: ARRAY_FILTER_USE_BOTH);

        self::$cache = array_filter([
            'command' => $event->getCommand()?->getName(),
            'arguments' => $filterParams($event->getInput()->getArguments()),
            'options' => $filterParams($event->getInput()->getOptions()),
        ]);
    }
}
