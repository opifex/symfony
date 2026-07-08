<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Domain\Account\Account;
use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\AccountRole;
use App\Domain\Account\AccountRoleSet;
use App\Domain\Account\AccountStatus;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use App\Domain\Account\Exception\AccountRevisionConflictException;
use App\Domain\Foundation\ValueObject\DateTimeUtc;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Domain\Foundation\ValueObject\PasswordHash;
use App\Domain\Localization\LocaleCode;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use App\Infrastructure\Doctrine\Repository\Account\AccountEntityRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class AccountEntityRepositoryTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(type: EntityManagerInterface::class);
        $this->accountEntityRepository = new AccountEntityRepository($this->entityManager);
    }

    public function testSaveThrowsWhenStoredVersionDiffersFromAccountVersion(): void
    {
        $account = $this->createAccount();
        $accountEntity = $this->createAccountEntity(id: $account->id->toString(), version: 2);

        $repository = $this->createMock(type: EntityRepository::class);
        $repository->method(constraint: 'findOneBy')->willReturn($accountEntity);
        $this->entityManager->method(constraint: 'getRepository')->willReturn($repository);
        $this->entityManager->expects($this->never())->method(constraint: 'flush');

        $this->expectException(AccountRevisionConflictException::class);

        (void) $this->accountEntityRepository->save($account);
    }

    public function testSaveConvertsOptimisticLockExceptionToDomainException(): void
    {
        $account = $this->createAccount();
        $accountEntity = $this->createAccountEntity(id: $account->id->toString(), version: 1);

        $repository = $this->createMock(type: EntityRepository::class);
        $repository->method(constraint: 'findOneBy')->willReturn($accountEntity);
        $this->entityManager->method(constraint: 'getRepository')->willReturn($repository);
        $this->entityManager
            ->method(constraint: 'flush')
            ->willThrowException(OptimisticLockException::lockFailed($accountEntity));

        $this->expectException(AccountRevisionConflictException::class);

        (void) $this->accountEntityRepository->save($account);
    }

    public function testSaveConvertsUniqueConstraintViolationToDomainException(): void
    {
        $account = $this->createAccount();

        $driverException = $this->createMock(type: DriverException::class);
        $repository = $this->createMock(type: EntityRepository::class);
        $repository->method(constraint: 'findOneBy')->willReturn(value: null);
        $this->entityManager->method(constraint: 'getRepository')->willReturn($repository);
        $this->entityManager
            ->method(constraint: 'flush')
            ->willThrowException(new UniqueConstraintViolationException($driverException, query: null));

        $this->expectException(AccountAlreadyExistsException::class);

        (void) $this->accountEntityRepository->save($account);
    }

    private function createAccount(): Account
    {
        $passwordHash = '$2y$12$abcdefghijklmnopqrstuuABCDEFGHIJKLMNOPQRSTUVWXYZ01234';

        return new Account(
            id: AccountIdentifier::fromString(uuid: '00000000-0000-6000-8000-000000000000'),
            email: EmailAddress::fromString(email: 'email@example.com'),
            password: PasswordHash::fromString($passwordHash),
            locale: LocaleCode::EnUs,
            roles: AccountRoleSet::fromRoles(roles: AccountRole::User),
            status: AccountStatus::Activated,
            createdAt: DateTimeUtc::now(),
            updatedAt: DateTimeUtc::now(),
            version: 1,
        );
    }

    private function createAccountEntity(string $id, int $version): AccountEntity
    {
        $passwordHash = '$2y$12$abcdefghijklmnopqrstuuABCDEFGHIJKLMNOPQRSTUVWXYZ01234';

        return new AccountEntity(
            id: $id,
            email: 'email@example.com',
            password: $passwordHash,
            locale: LocaleCode::EnUs->toString(),
            roles: [AccountRole::User->toString()],
            status: AccountStatus::Activated->toString(),
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
            version: $version,
        );
    }
}
