<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountRoleCollection;
use App\Domain\Entity\AccountStatus;
use App\Domain\Exception\AccountNotFoundException;
use App\Infrastructure\Security\AccountUser;
use App\Infrastructure\Security\AccountUserProvider;
use Codeception\Test\Unit;
use DateTimeImmutable;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Uid\Uuid;

final class AccountUserProviderTest extends Unit
{
    private AccountRepositoryInterface&MockObject $accountRepository;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(originalClassName: AccountRepositoryInterface::class);
    }

    public function testLoadUserByIdentifierWithEmail(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $account = new Account(
            uuid: Uuid::v7()->hash(),
            email: 'email@example.com',
            password: 'password4#account',
            locale: 'en_US',
            status: AccountStatus::Created,
            roles: new AccountRoleCollection(role: AccountRole::User),
            createdAt: new DateTimeImmutable(),
        );
        $accountUser = new AccountUser(
            identifier: $account->getUuid(),
            password: $account->getPassword(),
            roles: $account->getRoles()->toArray(),
            activated: true,
        );

        $this->accountRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByEmail')
            ->with($account->getEmail())
            ->willReturn($account);

        $loadedUser = $accountUserProvider->loadUserByIdentifier($account->getEmail());

        $this->assertEquals($accountUser->getUserIdentifier(), $loadedUser->getUserIdentifier());
        $this->assertEquals($accountUser->getRoles(), $loadedUser->getRoles());
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

    public function testRefreshUserThrowsUnsupportedUserException(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $accountUser = new AccountUser(
            identifier: Uuid::v7()->hash(),
            password: 'password4#account',
            roles: [AccountRole::User->value],
            activated: true,
        );

        $this->expectException(UnsupportedUserException::class);

        $accountUserProvider->refreshUser($accountUser);
    }

    public function testCheckSupportsClassWithMatchingClass(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $supports = $accountUserProvider->supportsClass(class: AccountUser::class);

        $this->assertTrue($supports);
    }

    public function testCheckSupportsClassWithNonMatchingClass(): void
    {
        $accountUserProvider = new AccountUserProvider($this->accountRepository);
        $supports = $accountUserProvider->supportsClass(class: stdClass::class);

        $this->assertFalse($supports);
    }
}
