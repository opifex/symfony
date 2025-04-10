<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class AuthorizationToken
{
    public function __construct(
        private readonly string $tokenIdentifier,
        private readonly string $userIdentifier,
    ) {
    }

    public function getTokenIdentifier(): string
    {
        return $this->tokenIdentifier;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }
}
