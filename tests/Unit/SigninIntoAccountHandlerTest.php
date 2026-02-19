<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Application\Command\SigninIntoAccount\SigninIntoAccountCommand;
use App\Application\Command\SigninIntoAccount\SigninIntoAccountCommandHandler;
use App\Application\Contract\AuthenticationRateLimiterInterface;
use App\Application\Contract\AuthorizationTokenStorageInterface;
use App\Application\Contract\JwtAccessTokenManagerInterface;
use App\Application\Exception\AuthorizationRequiredException;
use App\Application\Exception\AuthorizationThrottlingException;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use Override;
use PHPUnit\Framework\TestCase;

#[AllowDynamicProperties]
final class SigninIntoAccountHandlerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
        $this->authenticationRateLimiter = $this->createMock(type: AuthenticationRateLimiterInterface::class);
        $this->authorizationTokenStorage = $this->createMock(type: AuthorizationTokenStorageInterface::class);
        $this->jwtAccessTokenManager = $this->createMock(type: JwtAccessTokenManagerInterface::class);
    }

    public function testInvokeThrowsThrottlingException(): void
    {
        $handler = new SigninIntoAccountCommandHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authenticationRateLimiter: $this->authenticationRateLimiter,
            authorizationTokenStorage: $this->authorizationTokenStorage,
            jwtAccessTokenManager: $this->jwtAccessTokenManager,
        );

        $this->authorizationTokenStorage
            ->expects($this->once())
            ->method(constraint: 'getUserIdentifier')
            ->willThrowException(AuthorizationRequiredException::create());

        $this->authenticationRateLimiter
            ->expects($this->once())
            ->method(constraint: 'isAccepted')
            ->willThrowException(AuthorizationThrottlingException::create());

        $this->expectException(exception: AuthorizationThrottlingException::class);

        $handler(new SigninIntoAccountCommand());
    }

    public function testInvokeThrowsExceptionWhenAccountThrottled(): void
    {
        $handler = new SigninIntoAccountCommandHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authenticationRateLimiter: $this->authenticationRateLimiter,
            authorizationTokenStorage: $this->authorizationTokenStorage,
            jwtAccessTokenManager: $this->jwtAccessTokenManager,
        );

        $this->authorizationTokenStorage
            ->expects($this->once())
            ->method(constraint: 'getUserIdentifier')
            ->willThrowException(AuthorizationRequiredException::create());

        $this->authenticationRateLimiter
            ->expects($this->once())
            ->method(constraint: 'isAccepted')
            ->willReturn(value: false);

        $this->expectException(exception: AuthorizationThrottlingException::class);

        $handler(new SigninIntoAccountCommand());
    }
}
