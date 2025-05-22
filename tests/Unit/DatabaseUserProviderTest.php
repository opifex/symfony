<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\Account;
use App\Domain\Model\AccountRole;
use App\Domain\Model\AccountStatus;
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
    private AccountEntityRepositoryInterface&MockObject $accountEntityRepository;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
    }

    public function testLoadUserByIdentifierWithEmail(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountEntityRepository);
        $account = new Account(
            id: Uuid::v7()->hash(),
            createdAt: new DateTimeImmutable(),
            email: 'email@example.com',
            password: 'password4#account',
            locale: 'en_US',
            roles: [AccountRole::USER],
            status: AccountStatus::CREATED,
        );
        $passwordAuthenticatedUser = new PasswordAuthenticatedUser(
            userIdentifier: $account->getId(),
            password: $account->getPassword(),
            roles: $account->getRoles(),
            enabled: true,
        );

        $this->accountEntityRepository
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
        $databaseUserProvider = new DatabaseUserProvider($this->accountEntityRepository);

        $this->accountEntityRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->willThrowException(new AccountNotFoundException());

        $this->expectException(UserNotFoundException::class);

        $databaseUserProvider->loadUserByIdentifier(identifier: 'invalid@example.com');
    }

    public function testRefreshUserThrowsUnsupportedUserException(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountEntityRepository);
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
        $databaseUserProvider = new DatabaseUserProvider($this->accountEntityRepository);
        $supports = $databaseUserProvider->supportsClass(class: PasswordAuthenticatedUser::class);

        $this->assertTrue($supports);
    }

    public function testCheckSupportsClassWithNonMatchingClass(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountEntityRepository);
        $supports = $databaseUserProvider->supportsClass(class: stdClass::class);

        $this->assertFalse($supports);
    }
}
