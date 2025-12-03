<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\RateLimiter\AuthenticationRateLimiter;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

final class AuthenticationRateLimiterTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->rateLimiterFactory = $this->createMock(type: RateLimiterFactoryInterface::class);
    }

    public function testIsAcceptedThrowsExceptionForThrottlingEmail(): void
    {
        $authenticationRateLimiter = new AuthenticationRateLimiter($this->rateLimiterFactory);

        $rateLimit = $this->createConfiguredMock(
            type: RateLimit::class,
            configuration: ['isAccepted' => true],
        );

        $limiter = $this->createConfiguredMock(
            type: LimiterInterface::class,
            configuration: ['consume' => $rateLimit],
        );

        $this->rateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($limiter);

        $isAccepted = $authenticationRateLimiter->isAccepted(
            key: 'admin@example.com',
        );

        $this->assertTrue($isAccepted);
    }
}
