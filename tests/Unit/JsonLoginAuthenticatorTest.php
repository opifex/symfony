<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Security\Authenticator\JsonLoginAuthenticator;
use App\Infrastructure\Security\Exception\AuthorizationThrottlingException;
use JsonException;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class JsonLoginAuthenticatorTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->rateLimiterFactory = $this->createMock(type: RateLimiterFactoryInterface::class);
    }

    /**
     * @throws JsonException
     */
    public function testOnAuthenticationFailureThrowsThrottlingExceptionWhenRateLimitExceeded(): void
    {
        $authenticator = new JsonLoginAuthenticator($this->rateLimiterFactory);

        $request = Request::create(uri: '/api/auth/signin', method: 'POST', content: json_encode([
            'email' => 'email@example.com',
            'password' => 'password4#account',
        ], flags: JSON_THROW_ON_ERROR));

        $this->rateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn(
                $this->createConfiguredMock(
                    type: LimiterInterface::class,
                    configuration: [
                        'consume' => $this->createConfiguredMock(
                            type: RateLimit::class,
                            configuration: ['isAccepted' => false],
                        ),
                    ],
                ),
            );

        $this->expectException(exception: AuthorizationThrottlingException::class);

        $authenticator->onAuthenticationFailure($request, new AuthenticationException());
    }
}
