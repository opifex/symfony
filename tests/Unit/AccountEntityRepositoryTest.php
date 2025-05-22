<?php

declare(strict_types=1);

namespace Tests\Unit;

use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
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
}
