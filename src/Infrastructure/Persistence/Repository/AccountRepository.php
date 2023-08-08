<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountCollection;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\SortingOrder;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AccountNotFoundException;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\String\UnicodeString;

#[Autoconfigure(lazy: true)]
class AccountRepository extends AbstractRepository implements AccountRepositoryInterface
{
    public function findByCriteria(AccountSearchCriteria $criteria): AccountCollection
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(select: 'account')->from(from: Account::class, alias: 'account');

        if (!is_null($criteria->email)) {
            $builder->andWhere($builder->expr()->like(x: 'account.email', y: ':email'));
            $builder->setParameter(key: 'email', value: '%' . $criteria->email . '%', type: Types::STRING);
        }

        if (!is_null($criteria->status)) {
            $builder->andWhere($builder->expr()->eq(x: 'account.status', y: ':status'));
            $builder->setParameter(key: 'status', value: $criteria->status, type: Types::STRING);
        }

        if (!is_null($criteria->sort) && !is_null($criteria->order)) {
            $builder->orderBy(
                sort: 'account.' . (new UnicodeString($criteria->sort))->camel()->toString(),
                order: $criteria->order === SortingOrder::DESC ? SortingOrder::DESC : SortingOrder::ASC,
            );
        }

        $paginator = new Paginator($builder);
        $paginator->getQuery()->setFirstResult($criteria->offset)->setMaxResults($criteria->limit);

        return new AccountCollection($paginator);
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
        $builder->setParameter(key: 'email', value: $email, type: Types::STRING);

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
        $builder->select(select: 'account')->from(from: Account::class, alias: 'account');
        $builder->andWhere($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);

        try {
            /** @var Account */
            return $builder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw new AccountNotFoundException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws AccountNotFoundException
     */
    public function deleteByUuid(string $uuid): void
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->delete()->from(from: Account::class, alias: 'account');
        $builder->andWhere($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);

        if (!$builder->getQuery()->execute()) {
            throw new AccountNotFoundException();
        }
    }

    /**
     * @throws AccountAlreadyExistsException
     * @throws Exception
     */
    public function saveNewAccount(Account $account): void
    {
        try {
            $this->insertOne($account);
        } catch (UniqueConstraintViolationException $e) {
            throw new AccountAlreadyExistsException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
