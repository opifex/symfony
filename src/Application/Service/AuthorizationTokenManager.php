<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Application\Exception\AuthorizationForbiddenException;
use App\Application\Exception\AuthorizationRequiredException;
use App\Domain\Common\Role;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class AuthorizationTokenManager implements AuthorizationTokenManagerInterface
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    #[Override]
    public function getUserIdentifier(): ?string
    {
        return $this->tokenStorage->getToken()?->getUserIdentifier();
    }

    #[Override]
    public function checkUserPermission(Role $role, mixed $subject = null): void
    {
        if ($this->tokenStorage->getToken()?->getUserIdentifier() === null) {
            throw AuthorizationRequiredException::create();
        }

        if (!$this->authorizationChecker->isGranted($role->toString(), $subject)) {
            throw AuthorizationForbiddenException::create();
        }
    }
}
