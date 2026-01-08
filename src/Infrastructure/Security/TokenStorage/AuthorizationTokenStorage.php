<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\TokenStorage;

use App\Application\Contract\AuthorizationTokenStorageInterface;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AuthorizationTokenStorage implements AuthorizationTokenStorageInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    #[Override]
    public function getUserIdentifier(): ?string
    {
        return $this->tokenStorage->getToken()?->getUserIdentifier();
    }
}
