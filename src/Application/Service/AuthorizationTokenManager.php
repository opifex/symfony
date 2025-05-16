<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Authorization\AuthorizationRequiredException;
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

        if (!$userIdentifier) {
            throw AuthorizationRequiredException::create();
        }

        return $userIdentifier;
    }

    #[Override]
    public function checkPermission(string $access, mixed $subject = null): bool
    {
        if (!$this->tokenStorage->getToken()?->getUserIdentifier()) {
            throw AuthorizationRequiredException::create();
        }

        return $this->authorizationChecker->isGranted($access, $subject);
    }
}
