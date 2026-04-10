<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Application\Command\SigninIntoAccount\SigninIntoAccountCommand;
use App\Application\Command\SigninIntoAccount\SigninIntoAccountCommandHandler;
use App\Application\Contract\AuthenticationRateLimiterInterface;
use App\Application\Contract\AuthorizationTokenStorageInterface;
use App\Application\Contract\JwtAccessTokenIssuerInterface;
use App\Application\Exception\AuthorizationThrottlingException;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class SigninIntoAccountHandlerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
        $this->authenticationRateLimiter = $this->createMock(type: AuthenticationRateLimiterInterface::class);
        $this->authorizationTokenStorage = $this->createMock(type: AuthorizationTokenStorageInterface::class);
        $this->jwtAccessTokenIssuer = $this->createMock(type: JwtAccessTokenIssuerInterface::class);
    }

    public function testInvokeThrowsThrottlingException(): void
    {
        $handler = new SigninIntoAccountCommandHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authenticationRateLimiter: $this->authenticationRateLimiter,
            authorizationTokenStorage: $this->authorizationTokenStorage,
            jwtAccessTokenIssuer: $this->jwtAccessTokenIssuer,
        );

        $this->authenticationRateLimiter
            ->expects($this->once())
            ->method(constraint: 'isAccepted')
            ->willReturn(value: false);

        $this->expectException(exception: AuthorizationThrottlingException::class);

        $handler(new SigninIntoAccountCommand(email: 'admin@example.com', password: 'password4#account'));
    }
}
