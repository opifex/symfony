<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Service\MessagePrivacyProtector;
use Codeception\Test\Unit;

class MessagePrivacyProtectorTest extends Unit
{
    private MessagePrivacyProtector $messagePrivacyProtector;

    protected function setUp(): void
    {
        $this->messagePrivacyProtector = new MessagePrivacyProtector();
    }

    public function testProtectEmail(): void
    {
        $data = $this->messagePrivacyProtector->protect(['email' => 'admin@example.com']);

        $this->assertArrayHasKey(key: 'email', array: $data);
        $this->assertEquals(expected: 'a***n@example.com', actual: $data['email']);
    }

    public function testProtectPassword(): void
    {
        $data = $this->messagePrivacyProtector->protect(['password' => 'password']);

        $this->assertArrayHasKey(key: 'password', array: $data);
        $this->assertEquals(expected: '********', actual: $data['password']);
    }
}
