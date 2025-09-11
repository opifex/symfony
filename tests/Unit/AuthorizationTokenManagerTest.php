<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Service\AuthorizationTokenManager;
use App\Domain\Exception\Authorization\AuthorizationRequiredException;
use App\Domain\Model\Role;
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

    public function testGetUserIdentifierThrowsExceptionWithUnauthorizedUser(): void
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

        $authorizationTokenManager->getUserIdentifier();
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

        $authorizationTokenManager->checkUserPermission(role: Role::User);
    }
}
