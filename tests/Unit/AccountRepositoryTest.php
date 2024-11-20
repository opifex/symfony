<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\AccountStatus;
use App\Domain\Entity\SearchSorting;
use App\Domain\Entity\SortingOrder;
use App\Domain\Exception\AccountNotFoundException;
use App\Infrastructure\Persistence\Doctrine\Repository\AccountRepository;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;

final class AccountRepositoryTest extends Unit
{
    private EntityManagerInterface&MockObject $entityManager;

    private Query&MockObject $query;

    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(originalClassName: EntityManagerInterface::class);
        $this->query = $this->createMock(originalClassName: Query::class);

        $queryBuilder = $this->createMock(originalClassName: QueryBuilder::class);
        $expr = $this->createMock(originalClassName: Expr::class);

        $this->entityManager
            ->expects($this->once())
            ->method(constraint: 'createQueryBuilder')
            ->willReturn($queryBuilder);

        $queryBuilder
            ->expects($this->any())
            ->method(constraint: 'select')
            ->willReturn($queryBuilder);

        $queryBuilder
            ->expects($this->any())
            ->method(constraint: 'from')
            ->willReturn($queryBuilder);

        $queryBuilder
            ->expects($this->any())
            ->method(constraint: 'getQuery')
            ->willReturn($this->query);

        $queryBuilder
            ->expects($this->any())
            ->method(constraint: 'expr')
            ->willReturn($expr);
    }

    public function testFindByCriteriaWithInvalidSorting(): void
    {
        $accountRepository = new AccountRepository($this->entityManager);

        $this->expectException(LogicException::class);

        $sorting = new SearchSorting(field: 'invalid', order: SortingOrder::Asc);
        $criteria = new AccountSearchCriteria(sorting: $sorting);
        $accountRepository->findByCriteria($criteria);
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
            password: 'password4#account',
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
            status: AccountStatus::Activated,
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
