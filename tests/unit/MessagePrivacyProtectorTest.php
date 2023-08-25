<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Service\MessagePrivacyProtector;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;

class MessagePrivacyProtectorTest extends Unit
{
    protected function setUp(): void
    {
        $this->messagePrivacyProtector = new MessagePrivacyProtector();
    }

    #[DataProvider(methodName: 'privacyDataProvider')]
    public function testProtectPrivacyData(string $type, string $value, string $expected): void
    {
        $protectedMessage = $this->messagePrivacyProtector->protect([$type => $value]);

        $this->assertArrayHasKey(key: $type, array: $protectedMessage);
        $this->assertSame(expected: $expected, actual: $protectedMessage[$type]);
    }

    protected function privacyDataProvider(): array
    {
        return [
            ['type' => 'email', 'value' => 'admin@example.com', 'expected' => 'a***n@example.com'],
            ['type' => 'password', 'value' => 'password', 'expected' => '********'],
        ];
    }
}
