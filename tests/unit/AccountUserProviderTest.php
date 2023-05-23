<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Security\AccountUserProvider;
use App\Domain\Contract\Repository\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Exception\AccountNotFoundException;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Uid\UuidV6;

class AccountUserProviderTest extends Unit
{
    private AccountRepositoryInterface&MockObject $accountRepository;

    private AccountUserProvider $userProvider;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(originalClassName: AccountRepositoryInterface::class);
        $this->userProvider = new AccountUserProvider($this->accountRepository);
    }

    public function testLoadUserByIdentifierWithEmail(): void
    {
        $email = 'email@example.com';
        $account = new Account($email);
        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->with($email)
            ->willReturn($account);

        $loadedUser = $this->userProvider->loadUserByIdentifier($email);

        $this->assertSame($account, $loadedUser);
    }

    public function testLoadUserByIdentifierWithInvalidIdentifier(): void
    {
        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->willThrowException(new AccountNotFoundException());

        $this->expectException(UserNotFoundException::class);

        $this->userProvider->loadUserByIdentifier(identifier: 'invalid@example.com');
    }

    public function testLoadUserByIdentifierWithUuid(): void
    {
        $uuid = new UuidV6();
        $account = new Account(email: 'email@example.com');
        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByUuid')
            ->with($uuid)
            ->willReturn($account);

        $loadedUser = $this->userProvider->loadUserByIdentifier($uuid->toRfc4122());

        $this->assertSame($account, $loadedUser);
    }

    public function testRefreshUserThrowsUnsupportedUserException(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $this->userProvider->refreshUser(new Account(email: 'email@example.com'));
    }

    public function testSupportsClassWithMatchingClass(): void
    {
        $this->assertTrue($this->userProvider->supportsClass(class: Account::class));
    }

    public function testSupportsClassWithNonMatchingClass(): void
    {
        $this->assertFalse($this->userProvider->supportsClass(class: stdClass::class));
    }
}
