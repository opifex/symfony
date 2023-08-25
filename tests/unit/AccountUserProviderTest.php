<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Factory\AccountFactory;
use App\Application\Security\AccountUserProvider;
use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
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

        $this->accountUserProvider = new AccountUserProvider($this->accountRepository);
    }

    public function testLoadUserByIdentifierWithEmail(): void
    {
        $account = AccountFactory::createUserAccount(email: 'email@example.com');

        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->with($account->getEmail())
            ->willReturn($account);

        $loadedUser = $this->accountUserProvider->loadUserByIdentifier($account->getEmail());

        $this->assertSame($account, $loadedUser);
    }

    public function testLoadUserByIdentifierWithInvalidIdentifier(): void
    {
        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->willThrowException(new AccountNotFoundException());

        $this->expectException(UserNotFoundException::class);

        $this->accountUserProvider->loadUserByIdentifier(identifier: 'invalid@example.com');
    }

    public function testLoadUserByIdentifierWithUuid(): void
    {
        $uuid = new UuidV7();
        $account = AccountFactory::createUserAccount(email: 'email@example.com');

        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByUuid')
            ->with($uuid)
            ->willReturn($account);

        $loadedUser = $this->accountUserProvider->loadUserByIdentifier($uuid->toRfc4122());

        $this->assertSame($account, $loadedUser);
    }

    public function testRefreshUserThrowsUnsupportedUserException(): void
    {
        $account = AccountFactory::createUserAccount(email: 'email@example.com');

        $this->expectException(UnsupportedUserException::class);

        $this->accountUserProvider->refreshUser($account);
    }

    public function testSupportsClassWithMatchingClass(): void
    {
        $this->assertTrue($this->accountUserProvider->supportsClass(class: Account::class));
    }

    public function testSupportsClassWithNonMatchingClass(): void
    {
        $this->assertFalse($this->accountUserProvider->supportsClass(class: stdClass::class));
    }
}
