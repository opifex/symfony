<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Security\PasswordAuthenticatedUser;
use Codeception\Test\Unit;

final class PasswordAuthenticatedUserTest extends Unit
{
    public function testEraseCredentialsWithNoResult(): void
    {
        $passwordAuthenticatedUser = new PasswordAuthenticatedUser(
            userIdentifier: '00000000-0000-6000-8000-000000000000',
            password: 'password4#account',
        );
        $passwordAuthenticatedUser->eraseCredentials();

        $this->expectNotToPerformAssertions();
    }
}
