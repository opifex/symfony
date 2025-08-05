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
    public function getUserIdentifier(): string
    {
        $userIdentifier = $this->tokenStorage->getToken()?->getUserIdentifier();

        if (!is_string($userIdentifier)) {
            throw AuthorizationRequiredException::create();
        }

        return $userIdentifier;
    }

    #[Override]
    public function checkUserPermission(Role $role, mixed $subject = null): void
    {
        if (!$this->tokenStorage->getToken()?->getUserIdentifier()) {
            throw AuthorizationRequiredException::create();
        }

        if (!$this->authorizationChecker->isGranted($role->toString(), $subject)) {
            throw AuthorizationForbiddenException::create();
        }
    }
}
