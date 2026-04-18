<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Application\Exception\AuthorizationRequiredException;
use App\Infrastructure\Security\TokenStorage\AuthorizationTokenStorage;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class AuthorizationTokenStorageTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(type: TokenStorageInterface::class);
    }

    public function testGetUserIdentifierThrowsExceptionWithUnauthorizedUser(): void
    {
        $authorizationTokenStorage = new AuthorizationTokenStorage(
            tokenStorage: $this->tokenStorage,
        );

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(exception: AuthorizationRequiredException::class);

        $authorizationTokenStorage->getUserIdentifier();
    }

    public function testGetTokenIdentifierThrowsExceptionWithUnauthorizedUser(): void
    {
        $authorizationTokenStorage = new AuthorizationTokenStorage(
            tokenStorage: $this->tokenStorage,
        );

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(exception: AuthorizationRequiredException::class);

        $authorizationTokenStorage->getTokenIdentifier();
    }

    public function testGetTokenExpiresAtThrowsExceptionWithUnauthorizedUser(): void
    {
        $authorizationTokenStorage = new AuthorizationTokenStorage(
            tokenStorage: $this->tokenStorage,
        );

        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(exception: AuthorizationRequiredException::class);

        $authorizationTokenStorage->getTokenExpiresAt();
    }
}
