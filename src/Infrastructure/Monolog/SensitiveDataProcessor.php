<?php

declare(strict_types=1);

namespace App\Infrastructure\Monolog;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;

#[AsMonologProcessor]
final readonly class SensitiveDataProcessor
{
    /** @var array<string, string> */
    private const array PATTERNS = [
        'email' => '/(?<=.).(?=.*.{1}@)/u',
        'password' => '/./u',
    ];

    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(context: $this->protect($record->context));
    }

    /**
     * @param array<array-key, mixed> $data
     * @return array<array-key, mixed>
     */
    private function protect(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value) && array_key_exists($key, array: self::PATTERNS)) {
                $data[$key] = preg_replace(self::PATTERNS[$key], replacement: '*', subject: $value);
            } elseif (is_array($value)) {
                /** @var array<array-key, mixed> $value */
                $data[$key] = $this->protect($value);
            }
        }

        return $data;
    }
}
