<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Security\AuthenticatedUser\TokenAuthenticatedUser;
use PHPUnit\Framework\TestCase;

#[AllowDynamicProperties]
final class TokenAuthenticatedUserTest extends TestCase
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
