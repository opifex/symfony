<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Contract\AuthenticationRateLimiterInterface;
use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Application\Contract\JwtAccessTokenManagerInterface;
use App\Application\Exception\AuthorizationThrottlingException;
use App\Application\MessageHandler\Command\SigninIntoAccount\SigninIntoAccountCommand;
use App\Application\MessageHandler\Command\SigninIntoAccount\SigninIntoAccountCommandHandler;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use Override;
use PHPUnit\Framework\TestCase;

final class SigninIntoAccountHandlerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
        $this->authenticationRateLimiter = $this->createMock(type: AuthenticationRateLimiterInterface::class);
        $this->authorizationTokenManager = $this->createMock(type: AuthorizationTokenManagerInterface::class);
        $this->jwtAccessTokenManager = $this->createMock(type: JwtAccessTokenManagerInterface::class);
    }

    public function testInvokeThrowsThrottlingException(): void
    {
        $handler = new SigninIntoAccountCommandHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authenticationRateLimiter: $this->authenticationRateLimiter,
            authorizationTokenManager: $this->authorizationTokenManager,
            jwtAccessTokenManager: $this->jwtAccessTokenManager,
        );

        $this->authenticationRateLimiter
            ->expects($this->once())
            ->method(constraint: 'isAccepted')
            ->willThrowException(AuthorizationThrottlingException::create());

        $this->expectException(exception: AuthorizationThrottlingException::class);

        $handler(new SigninIntoAccountCommand());
    }

    public function testInvokeThrowsExceptionWhenAccountNotFound(): void
    {
        $handler = new SigninIntoAccountCommandHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authenticationRateLimiter: $this->authenticationRateLimiter,
            authorizationTokenManager: $this->authorizationTokenManager,
            jwtAccessTokenManager: $this->jwtAccessTokenManager,
        );

        $this->authorizationTokenManager
            ->expects($this->once())
            ->method(constraint: 'getUserIdentifier')
            ->willReturn(value: '00000000-0000-6000-8000-000000000000');

        $this->expectException(exception: AccountNotFoundException::class);

        $handler(new SigninIntoAccountCommand());
    }

    public function testInvokeThrowsExceptionWhenAccountThrottled(): void
    {
        $handler = new SigninIntoAccountCommandHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authenticationRateLimiter: $this->authenticationRateLimiter,
            authorizationTokenManager: $this->authorizationTokenManager,
            jwtAccessTokenManager: $this->jwtAccessTokenManager,
        );

        $this->authenticationRateLimiter
            ->expects($this->once())
            ->method(constraint: 'isAccepted')
            ->willReturn(value: false);

        $this->expectException(exception: AuthorizationThrottlingException::class);

        $handler(new SigninIntoAccountCommand());
    }
}
