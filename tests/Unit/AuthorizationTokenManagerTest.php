<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Exception\AuthorizationRequiredException;
use App\Application\Service\AuthorizationTokenManager;
use App\Domain\Account\AccountRole;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class AuthorizationTokenManagerTest extends TestCase
{
    private AuthorizationCheckerInterface&MockObject $authorizationChecker;

    private TokenStorageInterface&MockObject $tokenStorage;

    #[Override]
    protected function setUp(): void
    {
        $this->authorizationChecker = $this->createMock(type: AuthorizationCheckerInterface::class);
        $this->tokenStorage = $this->createMock(type: TokenStorageInterface::class);
    }

    public function testGetUserIdentifierReturnNullWithUnauthorizedUser(): void
    {
        $authorizationTokenManager = new AuthorizationTokenManager(
            authorizationChecker: $this->authorizationChecker,
            tokenStorage: $this->tokenStorage,
        );

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $userIdentifier = $authorizationTokenManager->getUserIdentifier();

        $this->assertSame(expected: null, actual: $userIdentifier);
    }

    public function testCheckPermissionThrowsExceptionWithUnauthorizedUser(): void
    {
        $authorizationTokenManager = new AuthorizationTokenManager(
            authorizationChecker: $this->authorizationChecker,
            tokenStorage: $this->tokenStorage,
        );

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(exception: AuthorizationRequiredException::class);

        $authorizationTokenManager->checkUserPermission(role: AccountRole::User);
    }
}
