<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Service\MessagePrivacyProtector;
use Codeception\Test\Unit;

class MessagePrivacyProtectorTest extends Unit
{
    protected function setUp(): void
    {
        $this->messagePrivacyProtector = new MessagePrivacyProtector();
    }

    public function testProtectEmailString(): void
    {
        $privacyMessage = ['email' => 'admin@example.com'];

        $protectedMessage = $this->messagePrivacyProtector->protect($privacyMessage);

        $this->assertArrayHasKey(key: 'email', array: $protectedMessage);
        $this->assertEquals(expected: 'a***n@example.com', actual: $protectedMessage['email']);
    }

    public function testProtectPasswordString(): void
    {
        $privacyMessage = ['password' => 'password'];

        $protectedMessage = $this->messagePrivacyProtector->protect($privacyMessage);

        $this->assertArrayHasKey(key: 'password', array: $protectedMessage);
        $this->assertEquals(expected: '********', actual: $protectedMessage['password']);
    }
}
