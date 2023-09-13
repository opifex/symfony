<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Service\MessagePrivacyProtector;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;

class MessagePrivacyProtectorTest extends Unit
{
    #[DataProvider(methodName: 'privacyDataProvider')]
    public function testProtectPrivacyData(string $type, string $value, string $expected): void
    {
        $messagePrivacyProtector = new MessagePrivacyProtector();
        $protectedMessage = $messagePrivacyProtector->protect([$type => $value]);

        $this->assertArrayHasKey(key: $type, array: $protectedMessage);
        $this->assertSame(expected: $expected, actual: $protectedMessage[$type]);
    }

    protected function privacyDataProvider(): array
    {
        return [
            ['type' => 'email', 'value' => 'admin@example.com', 'expected' => 'a***n@example.com'],
            ['type' => 'password', 'value' => 'password4#account', 'expected' => '*****************'],
        ];
    }
}
