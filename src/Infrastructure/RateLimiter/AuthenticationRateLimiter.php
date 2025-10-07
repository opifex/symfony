<?php

declare(strict_types=1);

namespace App\Infrastructure\RateLimiter;

use App\Application\Contract\AuthenticationRateLimiterInterface;
use Override;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

final class AuthenticationRateLimiter implements AuthenticationRateLimiterInterface
{
    public function __construct(
        private readonly RateLimiterFactoryInterface $authenticationLimiter,
    ) {
    }

    #[Override]
    public function isAccepted(string $key): bool
    {
        return $this->authenticationLimiter->create($key)->consume()->isAccepted();
    }
}
