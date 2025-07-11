<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\MessageHandler\SigninIntoAccount\SigninIntoAccountHandler;
use App\Application\MessageHandler\SigninIntoAccount\SigninIntoAccountRequest;
use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Contract\Authorization\AuthorizationTokenManagerInterface;
use App\Domain\Contract\Integration\JwtAccessTokenManagerInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;

final class SigninIntoAccountHandlerTest extends Unit
{
    private AccountEntityRepositoryInterface&MockObject $accountEntityRepository;

    private AuthorizationTokenManagerInterface&MockObject $authorizationTokenManager;

    private JwtAccessTokenManagerInterface&MockObject $jwtAccessTokenManager;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
        $this->authorizationTokenManager = $this->createMock(type: AuthorizationTokenManagerInterface::class);
        $this->jwtAccessTokenManager = $this->createMock(type: JwtAccessTokenManagerInterface::class);
    }

    public function testInvokeThrowsExceptionWhenAccountNotFound(): void
    {
        $handler = new SigninIntoAccountHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authorizationTokenManager: $this->authorizationTokenManager,
            jwtAccessTokenManager: $this->jwtAccessTokenManager,
        );

        $this->accountEntityRepository
            ->expects($this->once())
            ->method(constraint: 'findOneById')
            ->willReturn(value: null);

        $this->expectException(exception: AccountNotFoundException::class);

        $handler(new SigninIntoAccountRequest());
    }
}
