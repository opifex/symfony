<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Authorization\AuthorizationForbiddenException;
use App\Domain\Exception\Authorization\AuthorizationRequiredException;
use App\Domain\Model\Role;
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
