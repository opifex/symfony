<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Service\AccountTokenStorage;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Exception\AccountUnauthorizedException;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AccountTokenStorageTest extends Unit
{
    private AccountRepositoryInterface&MockObject $accountRepository;
    private TokenStorageInterface&MockObject $tokenStorage;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(type: AccountRepositoryInterface::class);
        $this->tokenStorage = $this->createMock(type: TokenStorageInterface::class);
    }

    public function testFetchAccountThrowsExceptionWithUnauthorizedUser(): void
    {
        $accountTokenStorage = new AccountTokenStorage($this->accountRepository, $this->tokenStorage);
        $this->tokenStorage
            ->expects($this->once())
            ->method(constraint: 'getToken')
            ->willReturn(value: null);

        $this->expectException(exception: AccountUnauthorizedException::class);

        $accountTokenStorage->getAccount();
    }
}
