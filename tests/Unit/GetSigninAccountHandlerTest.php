<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\MessageHandler\GetSigninAccount\GetSigninAccountHandler;
use App\Application\MessageHandler\GetSigninAccount\GetSigninAccountRequest;
use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;

final class GetSigninAccountHandlerTest extends Unit
{
    private AccountEntityRepositoryInterface&MockObject $accountEntityRepository;

    private AuthorizationTokenManagerInterface&MockObject $authorizationTokenManager;

    /**
     * @throws MockObjectException
     */
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
            ->method(constraint: 'findOneByid')
            ->willReturn(value: null);

        $this->expectException(exception: AccountNotFoundException::class);

        $handler(new GetSigninAccountRequest());
    }
}
