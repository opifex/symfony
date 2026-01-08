<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Contract\AuthorizationTokenManagerInterface;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AuthorizationTokenManager implements AuthorizationTokenManagerInterface
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
