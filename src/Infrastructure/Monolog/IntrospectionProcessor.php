<?php

declare(strict_types=1);

namespace App\Infrastructure\Monolog;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsMonologProcessor]
final class IntrospectionProcessor
{
    public function __construct(
        #[Autowire(param: 'kernel.project_dir')]
        private readonly string $kernelProjectDir,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        [$sourcesDir, $sourceOffset] = [$this->kernelProjectDir . '/src', strlen($this->kernelProjectDir) + 1];

        $backtrace = array_map(
            callback: static fn(array $trace): string => substr($trace['file'], $sourceOffset) . ':' . $trace['line'],
            array: array_filter(
                array: array_slice(debug_backtrace(options: DEBUG_BACKTRACE_IGNORE_ARGS, limit: 7), offset: 2),
                callback: static fn(array $trace): bool => isset($trace['file'], $trace['line'])
                    && str_starts_with($trace['file'], $sourcesDir),
            ),
        );

        if (count($backtrace) !== 0) {
            $record->extra['backtrace'] = array_values($backtrace);
        }

        return $record;
    }
}
