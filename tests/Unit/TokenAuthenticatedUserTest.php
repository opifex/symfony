<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Security\TokenAuthenticatedUser;
use Codeception\Test\Unit;

final class TokenAuthenticatedUserTest extends Unit
{
    public function testEraseCredentialsWithNoResult(): void
    {
        $tokenAuthenticatedUser = new TokenAuthenticatedUser(
            userIdentifier: '00000000-0000-6000-8000-000000000000',
        );
        $tokenAuthenticatedUser->eraseCredentials();

        $this->expectNotToPerformAssertions();
    }
}
