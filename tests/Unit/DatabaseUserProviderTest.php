<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use App\Domain\Exception\AccountNotFoundException;
use App\Infrastructure\Security\DatabaseUserProvider;
use App\Infrastructure\Security\PasswordAuthenticatedUser;
use Codeception\Test\Unit;
use DateTimeImmutable;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Uid\Uuid;

final class DatabaseUserProviderTest extends Unit
{
    private AccountRepositoryInterface&MockObject $accountRepository;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(type: AccountRepositoryInterface::class);
    }

    public function testLoadUserByIdentifierWithEmail(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountRepository);
        $account = new Account(
            uuid: Uuid::v7()->hash(),
            createdAt: new DateTimeImmutable(),
            email: 'email@example.com',
            password: 'password4#account',
            locale: 'en_US',
            roles: [AccountRole::USER],
            status: AccountStatus::CREATED,
        );
        $passwordAuthenticatedUser = new PasswordAuthenticatedUser(
            userIdentifier: $account->getUuid(),
            password: $account->getPassword(),
            roles: $account->getRoles(),
            enabled: true,
        );

        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->with($account->getEmail())
            ->willReturn($account);

        $loadedUser = $databaseUserProvider->loadUserByIdentifier($account->getEmail());

        $this->assertEquals($passwordAuthenticatedUser->getUserIdentifier(), $loadedUser->getUserIdentifier());
        $this->assertEquals($passwordAuthenticatedUser->getRoles(), $loadedUser->getRoles());
    }

    public function testLoadUserByIdentifierWithInvalidIdentifier(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountRepository);

        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->willThrowException(new AccountNotFoundException());

        $this->expectException(UserNotFoundException::class);

        $databaseUserProvider->loadUserByIdentifier(identifier: 'invalid@example.com');
    }

    public function testRefreshUserThrowsUnsupportedUserException(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountRepository);
        $passwordAuthenticatedUser = new PasswordAuthenticatedUser(
            userIdentifier: Uuid::v7()->hash(),
            password: 'password4#account',
            roles: [AccountRole::USER],
            enabled: true,
        );

        $this->expectException(UnsupportedUserException::class);

        $databaseUserProvider->refreshUser($passwordAuthenticatedUser);
    }

    public function testCheckSupportsClassWithMatchingClass(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountRepository);
        $supports = $databaseUserProvider->supportsClass(class: PasswordAuthenticatedUser::class);

        $this->assertTrue($supports);
    }

    public function testCheckSupportsClassWithNonMatchingClass(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountRepository);
        $supports = $databaseUserProvider->supportsClass(class: stdClass::class);

        $this->assertFalse($supports);
    }
}
