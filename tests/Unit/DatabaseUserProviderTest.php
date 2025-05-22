<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Model\Account;
use App\Domain\Model\AccountRole;
use App\Infrastructure\Security\DatabaseUserProvider;
use App\Infrastructure\Security\PasswordAuthenticatedUser;
use Codeception\Test\Unit;
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
        $account = Account::create(
            email: 'email@example.com',
            hashedPassword: 'password4#account',
            locale:'en_US',
        );
        $passwordAuthenticatedUser = new PasswordAuthenticatedUser(
            userIdentifier: $account->id,
            password: $account->password,
            roles: $account->roles,
            enabled: true,
        );

        $this->accountEntityRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->with($account->email)
            ->willReturn($account);

        $loadedUser = $databaseUserProvider->loadUserByIdentifier($account->email);

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
