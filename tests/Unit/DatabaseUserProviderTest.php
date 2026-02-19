<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Domain\Account\Account;
use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\AccountRole;
use App\Domain\Account\AccountRoleSet;
use App\Domain\Account\AccountStatus;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use App\Domain\Foundation\ValueObject\DateTimeUtc;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Domain\Foundation\ValueObject\PasswordHash;
use App\Domain\Localization\LocaleCode;
use App\Infrastructure\Security\AuthenticatedUser\PasswordAuthenticatedUser;
use App\Infrastructure\Security\UserProvider\DatabaseUserProvider;
use Override;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Uid\Uuid;

#[AllowDynamicProperties]
final class DatabaseUserProviderTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
    }

    public function testLoadUserByIdentifierWithEmail(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountEntityRepository);
        $account = new Account(
            id: AccountIdentifier::fromString(uuid: '00000000-0000-6000-8000-000000000000'),
            createdAt: DateTimeUtc::now(),
            email: EmailAddress::fromString(email: 'email@example.com'),
            password: PasswordHash::fromString(passwordHash: 'password4#account'),
            locale: LocaleCode::EnUs,
            roles: AccountRoleSet::fromStrings(AccountRole::User->toString()),
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
            ->with($account->getEmail())
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
            ->willThrowException(AccountNotFoundException::create());

        $this->expectException(UserNotFoundException::class);

        $databaseUserProvider->loadUserByIdentifier(identifier: 'invalid@example.com');
    }

    public function testRefreshUserThrowsUnsupportedUserException(): void
    {
        $databaseUserProvider = new DatabaseUserProvider($this->accountEntityRepository);
        $passwordAuthenticatedUser = new PasswordAuthenticatedUser(
            userIdentifier: Uuid::v7()->hash(),
            password: 'password4#account',
            roles: [AccountRole::User->toString()],
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
