<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Security\Authenticator\JsonLoginAuthenticator;
use DateMalformedStringException;
use JsonException;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
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
        $this->clock = new MockClock(now: '2026-01-01T00:00:00+00:00');
        $this->emailIpLoginRateLimiterFactory = $this->createMock(type: RateLimiterFactoryInterface::class);
        $this->ipLoginRateLimiterFactory = $this->createMock(type: RateLimiterFactoryInterface::class);
        $this->authenticator = new JsonLoginAuthenticator(
            $this->clock,
            $this->emailIpLoginRateLimiterFactory,
            $this->ipLoginRateLimiterFactory,
        );
    }

    /**
     * @return void
     * @throws DateMalformedStringException
     * @throws JsonException
     */
    public function testAuthenticateReturnsPassportWhenRateLimitNotExceeded(): void
    {
        $this->emailIpLoginRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 0));

        $this->ipLoginRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 0));

        self::assertInstanceOf(
            expected: Passport::class,
            actual: $this->authenticator->authenticate($this->createSigninRequest()),
        );
    }

    /**
     * @return void
     * @throws DateMalformedStringException
     * @throws JsonException
     */
    public function testAuthenticateThrowsThrottlingExceptionWithoutConsumingWhenEmailIpRateLimitAlreadyExceeded(): void
    {
        $this->emailIpLoginRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: false, expectedTokens: 0));

        $this->ipLoginRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 0));

        $this->expectException(exception: TooManyRequestsHttpException::class);

        $this->authenticator->authenticate($this->createSigninRequest());
    }

    /**
     * @return void
     * @throws DateMalformedStringException
     * @throws JsonException
     */
    public function testAuthenticateThrowsThrottlingExceptionWithoutConsumingWhenIpRateLimitAlreadyExceeded(): void
    {
        $this->emailIpLoginRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 0));

        $this->ipLoginRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: false, expectedTokens: 0));

        $this->expectException(exception: TooManyRequestsHttpException::class);

        $this->authenticator->authenticate($this->createSigninRequest());
    }

    /**
     * @return void
     * @throws DateMalformedStringException
     * @throws JsonException
     */
    public function testOnAuthenticationFailureConsumesOneTokenAndReturnsNullWhenRateLimitNotExceeded(): void
    {
        $this->emailIpLoginRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 1));

        $this->ipLoginRateLimiterFactory
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
     * @return void
     * @throws DateMalformedStringException
     * @throws JsonException
     */
    public function testOnAuthenticationFailureThrowsThrottlingExceptionWhenEmailIpRateLimitExceeded(): void
    {
        $this->emailIpLoginRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: false, expectedTokens: 1));

        $this->ipLoginRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 1));

        $this->expectException(exception: TooManyRequestsHttpException::class);

        $this->authenticator->onAuthenticationFailure($this->createSigninRequest(), new AuthenticationException());
    }

    /**
     * @return void
     * @throws DateMalformedStringException
     * @throws JsonException
     */
    public function testOnAuthenticationFailureThrowsThrottlingExceptionWhenIpRateLimitExceeded(): void
    {
        $this->emailIpLoginRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: true, expectedTokens: 1));

        $this->ipLoginRateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter(isAccepted: false, expectedTokens: 1));

        $this->expectException(exception: TooManyRequestsHttpException::class);

        $this->authenticator->onAuthenticationFailure($this->createSigninRequest(), new AuthenticationException());
    }

    /**
     * @throws JsonException
     * @throws DateMalformedStringException
     */
    #[DataProvider(methodName: 'retryAfterProvider')]
    public function testRetryAfterReflectsRejectedLimits(
        bool $emailIpAccepted,
        int $emailIpRetryAfter,
        bool $ipAccepted,
        int $ipRetryAfter,
        int $expectedRetryAfter,
    ): void {
        $this->emailIpLoginRateLimiterFactory
            ->method(constraint: 'create')
            ->willReturn(
                $this->createLimiter(
                    isAccepted: $emailIpAccepted,
                    expectedTokens: 1,
                    retryAfterSeconds: $emailIpRetryAfter,
                ),
            );
        $this->ipLoginRateLimiterFactory
            ->method(constraint: 'create')
            ->willReturn($this->createLimiter($ipAccepted, expectedTokens: 1, retryAfterSeconds: $ipRetryAfter));

        try {
            $this->authenticator->onAuthenticationFailure($this->createSigninRequest(), new AuthenticationException());
            self::fail(message: 'Expected a throttling exception.');
        } catch (TooManyRequestsHttpException $exception) {
            $retryAfter = $exception->getHeaders()['Retry-After'];
            self::assertSame($expectedRetryAfter, $retryAfter);
        }
    }

    public static function retryAfterProvider(): array
    {
        return [
            'email IP rejected, accepted IP has a later retry' => [false, 120, true, 900, 120],
            'IP rejected, accepted email IP has a later retry' => [true, 900, false, 120, 120],
            'both rejected, IP has a later retry' => [false, 120, false, 300, 300],
            'both rejected, email IP has a later retry' => [false, 300, false, 120, 300],
            'retry time has passed' => [false, -10, true, 0, 1],
        ];
    }

    /**
     * @throws DateMalformedStringException
     */
    private function createLimiter(
        bool $isAccepted,
        int $expectedTokens,
        int $retryAfterSeconds = 120,
    ): LimiterInterface {
        $limiter = $this->createMock(type: LimiterInterface::class);
        $limiter
            ->expects($this->once())
            ->method(constraint: 'consume')
            ->with($expectedTokens)
            ->willReturn(
                $this->createConfiguredMock(
                    type: RateLimit::class,
                    configuration: [
                        'isAccepted' => $isAccepted,
                        'getRetryAfter' => $this->clock->now()->modify(sprintf('%+d seconds', $retryAfterSeconds)),
                    ],
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
            uri: '/api/v1/auth/signin',
            method: 'POST',
            server: ['REMOTE_ADDR' => '203.0.113.10'],
            content: json_encode([
                'email' => 'email@example.com',
                'password' => 'password4#account',
            ], flags: JSON_THROW_ON_ERROR),
        );
    }
}
