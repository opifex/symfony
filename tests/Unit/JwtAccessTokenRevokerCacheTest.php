<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Cache\JwtAccessTokenRevokerCache;
use DateTimeImmutable;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Clock\MockClock;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class JwtAccessTokenRevokerCacheTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->cache = $this->createMock(type: CacheItemPoolInterface::class);
        $this->cacheItem = $this->createMock(type: CacheItemInterface::class);
        $this->clock = new MockClock(now: '2026-01-01T00:00:00+00:00');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testRevokeStoresTokenWithTtl(): void
    {
        $revocation = new JwtAccessTokenRevokerCache($this->cache, $this->clock);

        $this->cache
            ->expects($this->once())
            ->method(constraint: 'getItem')
            ->with('abc-jti-123')
            ->willReturn(value: $this->cacheItem);

        $this->cacheItem
            ->expects($this->once())
            ->method(constraint: 'set')
            ->with(true)
            ->willReturn(value: $this->cacheItem);

        $this->cacheItem
            ->expects($this->once())
            ->method(constraint: 'expiresAfter')
            ->with(3600)
            ->willReturn(value: $this->cacheItem);

        $this->cache
            ->expects($this->once())
            ->method(constraint: 'save')
            ->with($this->cacheItem);

        $revocation->revoke(
            tokenIdentifier: 'abc-jti-123',
            expiresAt: new DateTimeImmutable(datetime: '2026-01-01T01:00:00+00:00'),
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testRevokeIsNoOpWhenTokenAlreadyExpired(): void
    {
        $revocation = new JwtAccessTokenRevokerCache($this->cache, $this->clock);

        $this->cache
            ->expects($this->never())
            ->method(constraint: 'getItem');

        $revocation->revoke(
            tokenIdentifier: 'abc-jti-123',
            expiresAt: new DateTimeImmutable(datetime: '2025-12-31T23:59:59+00:00'),
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testIsRevokedReturnsTrueWhenItemExists(): void
    {
        $revocation = new JwtAccessTokenRevokerCache($this->cache, $this->clock);

        $this->cache
            ->expects($this->once())
            ->method(constraint: 'hasItem')
            ->with('abc-jti-123')
            ->willReturn(value: true);

        self::assertTrue($revocation->isRevoked(tokenIdentifier: 'abc-jti-123'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testIsRevokedReturnsFalseWhenItemDoesNotExist(): void
    {
        $revocation = new JwtAccessTokenRevokerCache($this->cache, $this->clock);

        $this->cache
            ->expects($this->once())
            ->method(constraint: 'hasItem')
            ->with('abc-jti-123')
            ->willReturn(value: false);

        self::assertFalse($revocation->isRevoked(tokenIdentifier: 'abc-jti-123'));
    }
}
