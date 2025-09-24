<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\MessageHandler\SigninIntoAccount\SigninIntoAccountHandler;
use App\Application\MessageHandler\SigninIntoAccount\SigninIntoAccountRequest;
use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Contract\Integration\JwtAccessTokenManagerInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Exception\Authorization\AuthorizationThrottlingException;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

final class SigninIntoAccountHandlerTest extends TestCase
{
    private AccountEntityRepositoryInterface&MockObject $accountEntityRepository;

    private AuthorizationTokenManagerInterface&MockObject $authorizationTokenManager;

    private JwtAccessTokenManagerInterface&MockObject $jwtAccessTokenManager;

    private RateLimiterFactoryInterface&MockObject $rateLimiterFactory;

    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
        $this->authorizationTokenManager = $this->createMock(type: AuthorizationTokenManagerInterface::class);
        $this->jwtAccessTokenManager = $this->createMock(type: JwtAccessTokenManagerInterface::class);
        $this->rateLimiterFactory = $this->createMock(type: RateLimiterFactoryInterface::class);
    }

    public function testInvokeThrowsThrottlingException(): void
    {
        $handler = new SigninIntoAccountHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authorizationTokenManager: $this->authorizationTokenManager,
            jwtAccessTokenManager: $this->jwtAccessTokenManager,
            authenticationLimiter: $this->rateLimiterFactory,
        );

        $rateLimit = $this->createConfiguredMock(
            type: RateLimit::class,
            configuration: ['isAccepted' => false],
        );

        $limiter = $this->createMock(type: LimiterInterface::class);
        $limiter->expects($this->once())
            ->method(constraint: 'consume')
            ->willReturn($rateLimit);

        $this->rateLimiterFactory
            ->expects($this->once())
            ->method(constraint: 'create')
            ->willReturn($limiter);

        $this->expectException(exception: AuthorizationThrottlingException::class);

        $handler(new SigninIntoAccountRequest());
    }

    public function testInvokeThrowsExceptionWhenAccountNotFound(): void
    {
        $handler = new SigninIntoAccountHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authorizationTokenManager: $this->authorizationTokenManager,
            jwtAccessTokenManager: $this->jwtAccessTokenManager,
            authenticationLimiter: $this->rateLimiterFactory,
        );

        $this->authorizationTokenManager
            ->expects($this->once())
            ->method(constraint: 'getUserIdentifier')
            ->willReturn(value: '00000000-0000-6000-8000-000000000000');

        $this->expectException(exception: AccountNotFoundException::class);

        $handler(new SigninIntoAccountRequest());
    }
}
