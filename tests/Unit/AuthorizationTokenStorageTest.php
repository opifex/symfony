<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Security\TokenStorage\AuthorizationTokenStorage;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AllowDynamicProperties]
final class AuthorizationTokenStorageTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(type: TokenStorageInterface::class);
    }

    public function testGetUserIdentifierReturnNullWithUnauthorizedUser(): void
    {
        $authorizationTokenStorage = new AuthorizationTokenStorage(
            tokenStorage: $this->tokenStorage,
        );

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $userIdentifier = $authorizationTokenStorage->getUserIdentifier();

        $this->assertSame(expected: null, actual: $userIdentifier);
    }
}
