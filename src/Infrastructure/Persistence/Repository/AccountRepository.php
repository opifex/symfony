<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Criteria\AccountSearchCriteria;
use App\Domain\Entity\Account;
use App\Domain\Exception\AccountNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\String\UnicodeString;

#[Autoconfigure(lazy: true)]
class AccountRepository implements AccountRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function findByCriteria(AccountSearchCriteria $criteria): iterable
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(select: 'account')->from(from: Account::class, alias: 'account');

        if (!is_null($criteria->email)) {
            $builder->andWhere($builder->expr()->like(x: 'account.email', y: ':email'));
            $builder->setParameter(key: 'email', value: '%' . $criteria->email . '%');
        }

        if (!is_null($criteria->status)) {
            $builder->andWhere($builder->expr()->eq(x: 'account.status', y: ':status'));
            $builder->setParameter(key: 'status', value: $criteria->status);
        }

        if (!is_null($criteria->sort) && !is_null($criteria->order)) {
            $builder->orderBy(
                sort: 'account.' . (new UnicodeString($criteria->sort))->camel()->toString(),
                order: $criteria->order === 'desc' ? 'desc' : 'asc',
            );
        }

        $paginator = new Paginator($builder);
        $paginator->getQuery()->setFirstResult($criteria->offset)->setMaxResults($criteria->limit);

        return $paginator;
    }

    /**
     * @throws AccountNotFoundException
     * @throws NonUniqueResultException
     */
    public function findOneByEmail(string $email): Account
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(select: 'account')->from(from: Account::class, alias: 'account');
        $builder->andWhere($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->setParameter(key: 'email', value: $email);

        try {
            /** @var Account */
            return $builder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw new AccountNotFoundException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws AccountNotFoundException
     * @throws NonUniqueResultException
     */
    public function findOneByUuid(string $uuid): Account
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(select: 'account');
        $builder->from(from: Account::class, alias: 'account');
        $builder->andWhere($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid);

        try {
            /** @var Account */
            return $builder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw new AccountNotFoundException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function persist(Account $account): void
    {
        $this->entityManager->persist($account);
        $this->entityManager->flush();
    }

    public function remove(Account $account): void
    {
        $this->entityManager->remove($account);
        $this->entityManager->flush();
    }
}
