<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Factory\AccountFactory;
use App\Application\Security\AccountUserProvider;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\LocaleCode;
use App\Domain\Exception\AccountNotFoundException;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use stdClass;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Uid\UuidV7;

class AccountUserProviderTest extends Unit
{
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(originalClassName: AccountRepositoryInterface::class);
    }

    public function testLoadUserByIdentifierWithEmail(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $account = AccountFactory::createUserAccount(email: 'email@example.com', locale: LocaleCode::EN);

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
        $account = AccountFactory::createUserAccount(email: 'email@example.com', locale: LocaleCode::EN);
        $uuid = new UuidV7();

        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByUuid')
            ->with($uuid)
            ->willReturn($account);

        $loadedUser = $accountUserProvider->loadUserByIdentifier($uuid->toRfc4122());

        $this->assertSame($account, $loadedUser);
    }

    public function testRefreshUserThrowsUnsupportedUserException(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $account = AccountFactory::createUserAccount(email: 'email@example.com', locale: LocaleCode::EN);

        $this->expectException(UnsupportedUserException::class);

        $accountUserProvider->refreshUser($account);
    }

    public function testSupportsClassWithMatchingClass(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $supports = $accountUserProvider->supportsClass(class: Account::class);

        $this->assertTrue($supports);
    }

    public function testSupportsClassWithNonMatchingClass(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $supports = $accountUserProvider->supportsClass(class: stdClass::class);

        $this->assertFalse($supports);
    }
}
