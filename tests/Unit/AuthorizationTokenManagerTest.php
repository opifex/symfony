<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Service\AuthorizationTokenManager;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AuthorizationTokenManagerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(type: TokenStorageInterface::class);
    }

    public function testGetUserIdentifierReturnNullWithUnauthorizedUser(): void
    {
        $authorizationTokenManager = new AuthorizationTokenManager(
            tokenStorage: $this->tokenStorage,
        );

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $userIdentifier = $authorizationTokenManager->getUserIdentifier();

        $this->assertSame(expected: null, actual: $userIdentifier);
    }
}
