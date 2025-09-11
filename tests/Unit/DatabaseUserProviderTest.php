<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Model\Account;
use App\Domain\Model\AccountIdentifier;
use App\Domain\Model\AccountRoles;
use App\Domain\Model\AccountStatus;
use App\Domain\Model\Common\DateTimeUtc;
use App\Domain\Model\Common\EmailAddress;
use App\Domain\Model\Common\HashedPassword;
use App\Domain\Model\LocaleCode;
use App\Domain\Model\Role;
use App\Infrastructure\Security\DatabaseUserProvider;
use App\Infrastructure\Security\PasswordAuthenticatedUser;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Uid\Uuid;

final class DatabaseUserProviderTest extends TestCase
{
    private AccountEntityRepositoryInterface&MockObject $accountEntityRepository;

    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
    }

    public function testLoadUserByIdentifierWithEmail(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountEntityRepository);
        $account = new Account(
            id: AccountIdentifier::generate(),
            createdAt: DateTimeUtc::now(),
            email: EmailAddress::fromString(email: 'email@example.com'),
            password: HashedPassword::fromString(passwordHash: 'password4#account'),
            locale: LocaleCode::EnUs,
            roles: AccountRoles::fromStrings(Role::User->toString()),
            status: AccountStatus::Created,
        );
        $passwordAuthenticatedUser = new PasswordAuthenticatedUser(
            userIdentifier: $account->getId()->toString(),
            password: $account->getPassword()->toString(),
            roles: $account->getRoles()->toArray(),
            enabled: true,
        );

        $this->accountEntityRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->with($account->getEmail()->toString())
            ->willReturn($account);

        $loadedUser = $databaseUserProvider->loadUserByIdentifier($account->getEmail()->toString());

        $this->assertEquals($passwordAuthenticatedUser->getUserIdentifier(), $loadedUser->getUserIdentifier());
        $this->assertEquals($passwordAuthenticatedUser->getRoles(), $loadedUser->getRoles());
    }

    public function testLoadUserByIdentifierWithInvalidIdentifier(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountEntityRepository);

        $this->accountEntityRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->willReturn(value: null);

        $this->expectException(UserNotFoundException::class);

        $databaseUserProvider->loadUserByIdentifier(identifier: 'invalid@example.com');
    }

    public function testRefreshUserThrowsUnsupportedUserException(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountEntityRepository);
        $passwordAuthenticatedUser = new PasswordAuthenticatedUser(
            userIdentifier: Uuid::v7()->hash(),
            password: 'password4#account',
            roles: [Role::User->toString()],
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
