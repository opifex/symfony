<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Application\Contract\AuthorizationTokenStorageInterface;
use App\Application\Exception\AuthorizationRequiredException;
use App\Application\Query\GetSigninAccount\GetSigninAccountQuery;
use App\Application\Query\GetSigninAccount\GetSigninAccountQueryHandler;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use Override;
use PHPUnit\Framework\TestCase;

#[AllowDynamicProperties]
final class GetSigninAccountHandlerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
        $this->authorizationTokenStorage = $this->createMock(type: AuthorizationTokenStorageInterface::class);
    }

    public function testInvokeThrowsAuthorizationRequiredException(): void
    {
        $handler = new GetSigninAccountQueryHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authorizationTokenStorage: $this->authorizationTokenStorage,
        );

        $this->expectException(exception: AuthorizationRequiredException::class);

        $handler(new GetSigninAccountQuery());
    }

    public function testInvokeThrowsExceptionWhenAccountNotFound(): void
    {
        $handler = new GetSigninAccountQueryHandler(
            accountEntityRepository: $this->accountEntityRepository,
            authorizationTokenStorage: $this->authorizationTokenStorage,
        );

        $this->authorizationTokenStorage
            ->expects($this->once())
            ->method(constraint: 'getUserIdentifier')
            ->willReturn(value: '00000000-0000-6000-8000-000000000000');

        $this->accountEntityRepository
            ->expects($this->once())
            ->method(constraint: 'findOneById')
            ->willReturn(value: null);

        $this->expectException(exception: AccountNotFoundException::class);

        $handler(new GetSigninAccountQuery());
    }
}
