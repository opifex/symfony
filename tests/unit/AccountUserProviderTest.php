<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Security\AccountUserProvider;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Exception\AccountNotFoundException;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use stdClass;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Uid\Uuid;

final class AccountUserProviderTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(originalClassName: AccountRepositoryInterface::class);
    }

    public function testLoadUserByIdentifierWithEmail(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $account = new Account(uuid: Uuid::v7()->toRfc4122(), email: 'email@example.com', password: '', locale: 'en');

        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->with($account->getEmail())
            ->willReturn($account);

        $loadedUser = $accountUserProvider->loadUserByIdentifier($account->getEmail());

        $this->assertSame($account, $loadedUser);
    }

    public function testLoadUserByIdentifierWithInvalidIdentifier(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);

        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->willThrowException(new AccountNotFoundException());

        $this->expectException(UserNotFoundException::class);

        $accountUserProvider->loadUserByIdentifier(identifier: 'invalid@example.com');
    }

    public function testLoadUserByIdentifierWithUuid(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $account = new Account(uuid: Uuid::v7()->toRfc4122(), email: 'email@example.com', password: '', locale: 'en');

        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByUuid')
            ->with($account->getUuid())
            ->willReturn($account);

        $loadedUser = $accountUserProvider->loadUserByIdentifier($account->getUuid());

        $this->assertSame($account, $loadedUser);
    }

    public function testRefreshUserThrowsUnsupportedUserException(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $account = new Account(uuid: Uuid::v7()->toRfc4122(), email: 'email@example.com', password: '', locale: 'en');

        $this->expectException(UnsupportedUserException::class);

        $accountUserProvider->refreshUser($account);
    }

    public function testCheckSupportsClassWithMatchingClass(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $supports = $accountUserProvider->supportsClass(class: Account::class);

        $this->assertTrue($supports);
    }

    public function testCheckSupportsClassWithNonMatchingClass(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $supports = $accountUserProvider->supportsClass(class: stdClass::class);

        $this->assertFalse($supports);
    }
}
