<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\AuthenticatedUser;

use DateTimeImmutable;
use Override;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TokenAuthenticatedUser implements UserInterface
{
    /**
     * @param string[] $userRoles
     */
    public function __construct(
        private string $tokenIdentifier,
        private DateTimeImmutable $tokenExpiresAt,
        private string $userIdentifier,
        private array $userRoles,
    ) {
    }

    public function getTokenIdentifier(): string
    {
        return $this->tokenIdentifier;
    }

    public function getTokenExpiresAt(): DateTimeImmutable
    {
        return $this->tokenExpiresAt;
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        /** @var non-empty-string */
        return $this->userIdentifier;
    }

    #[Override]
    public function getRoles(): array
    {
        return $this->userRoles;
    }
}
