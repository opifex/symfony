<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\AccountStatus;
use App\Infrastructure\Doctrine\Repository\Account\AccountEntityRepository;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;

final class AccountEntityRepositoryTest extends Unit
{
    private EntityManagerInterface&MockObject $entityManager;

    private Query&MockObject $query;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(type: EntityManagerInterface::class);
        $this->query = $this->createMock(type: Query::class);

        $queryBuilder = $this->createMock(type: QueryBuilder::class);
        $expr = $this->createMock(type: Expr::class);

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

    public function testFindStatusById(): void
    {
        $accountEntityRepository = new AccountEntityRepository($this->entityManager);

        $this->query
            ->expects($this->once())
            ->method(constraint: 'getSingleScalarResult')
            ->willThrowException(new NoResultException());

        $this->expectException(AccountNotFoundException::class);

        $accountEntityRepository->getStatusById(
            id: '00000000-0000-6000-8000-000000000000',
        );
    }

    public function testUpdateEmailById(): void
    {
        $accountEntityRepository = new AccountEntityRepository($this->entityManager);

        $this->query
            ->expects($this->once())
            ->method(constraint: 'execute')
            ->willReturn(value: false);

        $this->expectException(AccountNotFoundException::class);

        $accountEntityRepository->updateEmailById(
            id: '00000000-0000-6000-8000-000000000000',
            email: 'email@example.com',
        );
    }

    public function testUpdatePasswordById(): void
    {
        $accountEntityRepository = new AccountEntityRepository($this->entityManager);

        $this->query
            ->expects($this->once())
            ->method(constraint: 'execute')
            ->willReturn(value: false);

        $this->expectException(AccountNotFoundException::class);

        $accountEntityRepository->updatePasswordById(
            id: '00000000-0000-6000-8000-000000000000',
            password: 'password4#account',
        );
    }

    public function testUpdateStatusById(): void
    {
        $accountEntityRepository = new AccountEntityRepository($this->entityManager);

        $this->query
            ->expects($this->once())
            ->method(constraint: 'execute')
            ->willReturn(value: false);

        $this->expectException(AccountNotFoundException::class);

        $accountEntityRepository->updateStatusById(
            id: '00000000-0000-6000-8000-000000000000',
            status: AccountStatus::ACTIVATED,
        );
    }

    public function testUpdateLocaleById(): void
    {
        $accountEntityRepository = new AccountEntityRepository($this->entityManager);

        $this->query
            ->expects($this->once())
            ->method(constraint: 'execute')
            ->willReturn(value: false);

        $this->expectException(AccountNotFoundException::class);

        $accountEntityRepository->updateLocaleById(
            id: '00000000-0000-6000-8000-000000000000',
            locale: 'en_US',
        );
    }
}
