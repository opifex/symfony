<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Application\Contract\JwtAccessTokenRevokerInterface;
use DateTimeImmutable;
use Override;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class JwtAccessTokenRevokerCache implements JwtAccessTokenRevokerInterface
{
    public function __construct(
        #[Autowire(service: 'cache.token_revocation')]
        private CacheItemPoolInterface $cacheItemPool,
        private ClockInterface $clock,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    public function revoke(string $tokenIdentifier, DateTimeImmutable $expiresAt): void
    {
        $ttl = $expiresAt->getTimestamp() - $this->clock->now()->getTimestamp();

        if ($ttl <= 0) {
            return;
        }

        $cacheItem = $this->cacheItemPool->getItem($tokenIdentifier);
        $cacheItem->set(value: true);
        $cacheItem->expiresAfter($ttl);

        $this->cacheItemPool->save($cacheItem);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    public function isRevoked(string $tokenIdentifier): bool
    {
        return $this->cacheItemPool->hasItem($tokenIdentifier);
    }
}
