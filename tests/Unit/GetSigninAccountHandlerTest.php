<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Contract\AuthorizationTokenManagerInterface;
use App\Application\Exception\AuthorizationRequiredException;
use App\Application\MessageHandler\GetSigninAccount\GetSigninAccountHandler;
use App\Application\MessageHandler\GetSigninAccount\GetSigninAccountRequest;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use Override;
use PHPUnit\Framework\TestCase;

final class GetSigninAccountHandlerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
        $this->authorizationTokenManager = $this->createMock(type: AuthorizationTokenManagerInterface::class);
    }

    public function testInvokeThrowsAuthorizationRequiredException(): void
    {
        $handler = new GetSigninAccountHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authorizationTokenManager: $this->authorizationTokenManager,
        );

        $this->expectException(exception: AuthorizationRequiredException::class);

        $handler(new GetSigninAccountRequest());
    }

    public function testInvokeThrowsExceptionWhenAccountNotFound(): void
    {
        $handler = new GetSigninAccountHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authorizationTokenManager: $this->authorizationTokenManager,
        );

        $this->authorizationTokenManager
            ->expects($this->once())
            ->method(constraint: 'getUserIdentifier')
            ->willReturn(value: '00000000-0000-6000-8000-000000000000');

        $this->accountEntityRepository
            ->expects($this->once())
            ->method(constraint: 'findOneById')
            ->willReturn(value: null);

        $this->expectException(exception: AccountNotFoundException::class);

        $handler(new GetSigninAccountRequest());
    }
}
