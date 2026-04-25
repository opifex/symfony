<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\TokenStorage;

use App\Application\Contract\AuthorizationTokenStorageInterface;
use App\Infrastructure\Security\AuthenticatedUser\TokenAuthenticatedUser;
use DateTimeImmutable;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final readonly class AuthorizationTokenStorage implements AuthorizationTokenStorageInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    #[Override]
    public function getTokenIdentifier(): string
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof TokenAuthenticatedUser) {
            throw new AuthenticationException(message: 'Unable to retrieve token identifier.');
        }

        return $user->getTokenIdentifier();
    }

    #[Override]
    public function getTokenExpiresAt(): DateTimeImmutable
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof TokenAuthenticatedUser) {
            throw new AuthenticationException(message: 'Unable to retrieve token expiry.');
        }

        return $user->getTokenExpiresAt();
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        $user = $this->tokenStorage->getToken();

        if (!$user instanceof TokenInterface) {
            throw new AuthenticationException(message: 'Invalid authorization credentials provided.');
        }

        return $user->getUserIdentifier();
    }
}
