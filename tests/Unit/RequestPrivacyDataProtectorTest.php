<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Service\RequestPrivacyDataProtector;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;

final class RequestPrivacyDataProtectorTest extends Unit
{
    #[DataProvider(methodName: 'requestDataProvider')]
    public function testProtectRequestData(array $value, array $expected): void
    {
        $requestPrivacyDataProtector = new RequestPrivacyDataProtector();
        $protectedMessage = $requestPrivacyDataProtector->protect($value);

        $this->assertSame($expected, $protectedMessage);
    }

    protected function requestDataProvider(): array
    {
        return [
            ['value' => ['email' => 'admin@example.com'], 'expected' => ['email' => 'a***n@example.com']],
            ['value' => ['password' => 'password4#account'], 'expected' => ['password' => '*****************']],
            ['value' => [['email' => 'admin@example.com']], 'expected' => [['email' => 'a***n@example.com']]],
        ];
    }
}
