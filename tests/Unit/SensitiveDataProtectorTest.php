<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Security\SensitiveDataProtector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SensitiveDataProtectorTest extends TestCase
{
    #[DataProvider(methodName: 'maskingDataProvider')]
    public function testProtectMasksMatchingFields(array $data, array $patterns, array $expected): void
    {
        $sensitiveDataProtector = new SensitiveDataProtector();

        self::assertSame($expected, $sensitiveDataProtector->protect($data, $patterns));
    }

    public static function maskingDataProvider(): iterable
    {
        yield 'mask single email in array' => [
            'data' => ['email' => 'admin@example.com'],
            'patterns' => ['email' => '/(?<=.).(?=.*.{1}@)/u'],
            'expected' => ['email' => 'a***n@example.com'],
        ];
        yield 'mask single password in array' => [
            'data' => ['password' => 'password4#account'],
            'patterns' => ['password' => '/\G(?:(?<=^.{0,6}).|.+)/su'],
            'expected' => ['password' => '********'],
        ];
        yield 'mask email in nested array' => [
            'data' => [['email' => 'admin@example.com']],
            'patterns' => ['email' => '/(?<=.).(?=.*.{1}@)/u'],
            'expected' => [['email' => 'a***n@example.com']],
        ];
    }
}
