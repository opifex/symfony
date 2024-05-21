<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use App\Domain\Exception\AccountNotFoundException;
use App\Infrastructure\Persistence\Doctrine\Repository\AccountRepository;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Override;
use PHPUnit\Framework\MockObject\Exception;

final class AccountRepositoryTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(originalClassName: EntityManagerInterface::class);
        $this->queryBuilder = $this->createMock(originalClassName: QueryBuilder::class);
        $this->query = $this->createMock(originalClassName: Query::class);
        $this->expr = $this->createMock(originalClassName: Expr::class);

        $this->entityManager
            ->expects($this->once())
            ->method(constraint: 'createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->expects($this->once())
            ->method(constraint: 'getQuery')
            ->willReturn($this->query);

        $this->queryBuilder
            ->expects($this->once())
            ->method(constraint: 'expr')
            ->willReturn($this->expr);
    }

    public function testUpdateEmailByUuid(): void
    {
        $accountRepository = new AccountRepository($this->entityManager);

        $this->query
            ->expects($this->once())
            ->method(constraint: 'execute')
            ->willReturn(value: false);

        $this->expectException(AccountNotFoundException::class);

        $accountRepository->updateEmailByUuid(
            uuid: '00000000-0000-6000-8000-000000000000',
            email: 'email@example.com',
        );
    }

    public function testUpdatePasswordByUuid(): void
    {
        $accountRepository = new AccountRepository($this->entityManager);

        $this->query
            ->expects($this->once())
            ->method(constraint: 'execute')
            ->willReturn(value: false);

        $this->expectException(AccountNotFoundException::class);

        $accountRepository->updatePasswordByUuid(
            uuid: '00000000-0000-6000-8000-000000000000',
            password: 'password',
        );
    }

    public function testUpdateStatusByUuid(): void
    {
        $accountRepository = new AccountRepository($this->entityManager);

        $this->query
            ->expects($this->once())
            ->method(constraint: 'execute')
            ->willReturn(value: false);

        $this->expectException(AccountNotFoundException::class);

        $accountRepository->updateStatusByUuid(
            uuid: '00000000-0000-6000-8000-000000000000',
            status: AccountStatus::ACTIVATED,
        );
    }

    public function testUpdateRolesByUuid(): void
    {
        $accountRepository = new AccountRepository($this->entityManager);

        $this->query
            ->expects($this->once())
            ->method(constraint: 'execute')
            ->willReturn(value: false);

        $this->expectException(AccountNotFoundException::class);

        $accountRepository->updateRolesByUuid(
            uuid: '00000000-0000-6000-8000-000000000000',
            roles: [AccountRole::ROLE_USER],
        );
    }

    public function testUpdateLocaleByUuid(): void
    {
        $accountRepository = new AccountRepository($this->entityManager);

        $this->query
            ->expects($this->once())
            ->method(constraint: 'execute')
            ->willReturn(value: false);

        $this->expectException(AccountNotFoundException::class);

        $accountRepository->updateLocaleByUuid(
            uuid: '00000000-0000-6000-8000-000000000000',
            locale: 'en_US',
        );
    }
}
