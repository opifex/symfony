<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Monolog\SensitiveDataProcessor;
use DateTimeImmutable;
use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class SensitiveDataProcessorTest extends TestCase
{
    #[DataProvider(methodName: 'requestDataProvider')]
    public function testProtectLogRecordContext(array $data, array $expected): void
    {
        $processed = new SensitiveDataProcessor()(
            new LogRecord(
                datetime: new DateTimeImmutable(),
                channel: 'test',
                level: Level::Info,
                message: 'test',
                context: $data,
            ),
        );

        self::assertSame($expected, $processed->context);
    }

    public static function requestDataProvider(): iterable
    {
        yield 'mask single email in array' => [
            'data' => ['email' => 'admin@example.com'],
            'expected' => ['email' => 'a***n@example.com'],
        ];
        yield 'mask single password in array' => [
            'data' => ['password' => 'password4#account'],
            'expected' => ['password' => '*****************'],
        ];
        yield 'mask email in nested array' => [
            'data' => [['email' => 'admin@example.com']],
            'expected' => [['email' => 'a***n@example.com']],
        ];
    }
}
