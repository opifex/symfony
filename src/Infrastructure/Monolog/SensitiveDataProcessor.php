<?php

declare(strict_types=1);

namespace App\Infrastructure\Monolog;

use App\Infrastructure\Security\SensitiveDataProtector;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;

#[AsMonologProcessor(priority: -100)]
final readonly class SensitiveDataProcessor
{
    /** @var array<string, string> */
    private const array PATTERNS = [
        'email' => '/(?<=.).(?=.*.{1}@)/u',
        'password' => '/./u',
    ];

    public function __construct(
        private SensitiveDataProtector $sensitiveDataProtector,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(
            context: $this->sensitiveDataProtector->protect(
                data: $record->context,
                patterns: self::PATTERNS,
            ),
        );
    }
}
