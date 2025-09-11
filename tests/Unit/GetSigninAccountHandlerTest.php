<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\MessageHandler\GetSigninAccount\GetSigninAccountHandler;
use App\Application\MessageHandler\GetSigninAccount\GetSigninAccountRequest;
use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetSigninAccountHandlerTest extends TestCase
{
    private AccountEntityRepositoryInterface&MockObject $accountEntityRepository;

    private AuthorizationTokenManagerInterface&MockObject $authorizationTokenManager;

    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
        $this->authorizationTokenManager = $this->createMock(type: AuthorizationTokenManagerInterface::class);
    }

    public function testInvokeThrowsExceptionWhenAccountNotFound(): void
    {
        $handler = new GetSigninAccountHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authorizationTokenManager: $this->authorizationTokenManager,
        );

        $this->accountEntityRepository
            ->expects($this->once())
            ->method(constraint: 'findOneById')
            ->willReturn(value: null);

        $this->expectException(exception: AccountNotFoundException::class);

        $handler(new GetSigninAccountRequest());
    }
}
