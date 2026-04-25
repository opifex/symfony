<?php

declare(strict_types=1);

namespace App\Infrastructure\RateLimiter;

use App\Application\Contract\AuthenticationRateLimiterInterface;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

final readonly class AuthenticationRateLimiter implements AuthenticationRateLimiterInterface
{
    public function __construct(
        #[Autowire(service: 'limiter.authentication')]
        private RateLimiterFactoryInterface $rateLimiterFactory,
    ) {
    }

    #[Override]
    public function isAccepted(string $key): bool
    {
        return $this->rateLimiterFactory->create(sha1($key))->consume()->isAccepted();
    }
}
