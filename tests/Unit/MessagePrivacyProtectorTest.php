<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\Service\MessagePrivacyProtector;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;

final class MessagePrivacyProtectorTest extends Unit
{
    #[DataProvider(methodName: 'privacyDataProvider')]
    public function testProtectPrivacyData(array $value, array $expected): void
    {
        $messagePrivacyProtector = new MessagePrivacyProtector();
        $protectedMessage = $messagePrivacyProtector->protect($value);

        $this->assertSame($expected, $protectedMessage);
    }

    protected function privacyDataProvider(): array
    {
        return [
            ['value' => ['email' => 'admin@example.com'], 'expected' => ['email' => 'a***n@example.com']],
            ['value' => ['password' => 'password4#account'], 'expected' => ['password' => '*****************']],
            ['value' => [['email' => 'admin@example.com']], 'expected' => [['email' => 'a***n@example.com']]],
        ];
    }
}
