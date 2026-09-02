<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Security\Authenticator\JsonLoginAuthenticator;
use JsonException;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class JsonLoginAuthenticatorTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->emailIpRateLimiterFactory = $this->createMock(type: RateLimiterFactoryInterface::class);
        $this->ipRateLimiterFactory = $this->createMock(type: RateLimiterFactoryInterface::class);
        $this->authenticator = new JsonLoginAuthenticator(
            $this->emailIpRateLimiterFactory,
            $this->ipRateLimiterFactory,
        );
    }

    /**
     * @throws JsonException
     */
    public function testAuthenticateReturnsPassportWhenRateLimitNotExceeded(): void
    {
        $this->emailIpRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 0));

        $this->ipRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 0));

        self::assertInstanceOf(
            expected: Passport::class,
            actual: $this->authenticator->authenticate($this->createSigninRequest()),
        );
    }

    /**
     * @throws JsonException
     */
    public function testAuthenticateThrowsThrottlingExceptionWithoutConsumingWhenEmailIpRateLimitAlreadyExceeded(): void
    {
        $this->emailIpRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: false, expectedTokens: 0));

        $this->ipRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 0));

        $this->expectException(exception: TooManyRequestsHttpException::class);

        $this->authenticator->authenticate($this->createSigninRequest());
    }

    /**
     * @throws JsonException
     */
    public function testAuthenticateThrowsThrottlingExceptionWithoutConsumingWhenIpRateLimitAlreadyExceeded(): void
    {
        $this->emailIpRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 0));

        $this->ipRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: false, expectedTokens: 0));

        $this->expectException(exception: TooManyRequestsHttpException::class);

        $this->authenticator->authenticate($this->createSigninRequest());
    }

    /**
     * @throws JsonException
     */
    public function testOnAuthenticationFailureConsumesOneTokenAndReturnsNullWhenRateLimitNotExceeded(): void
    {
        $this->emailIpRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 1));

        $this->ipRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 1));

        $result = $this->authenticator->onAuthenticationFailure(
            $this->createSigninRequest(),
            new AuthenticationException(),
        );

        self::assertNull($result);
    }

    /**
     * @throws JsonException
     */
    public function testOnAuthenticationFailureThrowsThrottlingExceptionWhenEmailIpRateLimitExceeded(): void
    {
        $this->emailIpRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: false, expectedTokens: 1));

        $this->ipRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 1));

        $this->expectException(exception: TooManyRequestsHttpException::class);

        $this->authenticator->onAuthenticationFailure($this->createSigninRequest(), new AuthenticationException());
    }

    /**
     * @throws JsonException
     */
    public function testOnAuthenticationFailureThrowsThrottlingExceptionWhenIpRateLimitExceeded(): void
    {
        $this->emailIpRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 1));

        $this->ipRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: false, expectedTokens: 1));

        $this->expectException(exception: TooManyRequestsHttpException::class);

        $this->authenticator->onAuthenticationFailure($this->createSigninRequest(), new AuthenticationException());
    }

    private function createLimiter(bool $isAccepted, int $expectedTokens): LimiterInterface
    {
        $limiter = $this->createMock(type: LimiterInterface::class);
        $limiter
            ->expects($this->once())
            ->method(constraint: 'consume')
            ->with($expectedTokens)
            ->willReturn(
                $this->createConfiguredMock(
                    type: RateLimit::class,
                    configuration: ['isAccepted' => $isAccepted],
                ),
            );

        return $limiter;
    }

    /**
     * @throws JsonException
     */
    private function createSigninRequest(): Request
    {
        return Request::create(
            uri: '/api/auth/signin',
            method: 'POST',
            server: ['REMOTE_ADDR' => '203.0.113.10'],
            content: json_encode([
                'email' => 'email@example.com',
                'password' => 'password4#account',
            ], flags: JSON_THROW_ON_ERROR),
        );
    }
}
