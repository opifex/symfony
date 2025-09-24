<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Service\RequestPrivacyDataProtector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RequestPrivacyDataProtectorTest extends TestCase
{
    #[DataProvider(methodName: 'requestDataProvider')]
    public function testProtectRequestData(array $data, array $expected): void
    {
        $requestPrivacyDataProtector = new RequestPrivacyDataProtector();
        $protectedMessage = $requestPrivacyDataProtector->protect($data);

        $this->assertSame($expected, $protectedMessage);
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
