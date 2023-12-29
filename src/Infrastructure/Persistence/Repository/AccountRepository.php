<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountCollection;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AccountNotFoundException;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class AccountRepository extends AbstractRepository implements AccountRepositoryInterface
{
    #[Override]
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

        if (!is_null($criteria->sorting)) {
            $sortingFieldsMapping = [
                AccountSearchCriteria::FIELD_CREATED_AT => 'account.createdAt',
                AccountSearchCriteria::FIELD_EMAIL => 'account.email',
                AccountSearchCriteria::FIELD_STATUS => 'account.status',
            ];
            $builder->orderBy($this->buildOrderBy($criteria->sorting, $sortingFieldsMapping));
        }

        $builder->setFirstResult($criteria->pagination?->offset);
        $builder->setMaxResults($criteria->pagination?->limit);

        return new AccountCollection(new Paginator($builder));
    }

    /**
     * @throws AccountNotFoundException
     * @throws NonUniqueResultException
     */
    #[Override]
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
            throw new AccountNotFoundException(
                message: 'Account with provided identifier not found.',
                previous: $e,
            );
        }
    }

    /**
     * @throws AccountNotFoundException
     * @throws NonUniqueResultException
     */
    #[Override]
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
            throw new AccountNotFoundException(
                message: 'Account with provided identifier not found.',
                previous: $e,
            );
        }
    }

    /**
     * @throws AccountAlreadyExistsException
     * @throws Exception
     */
    #[Override]
    public function insert(Account $account): void
    {
        try {
            $this->insertOne($account);
        } catch (UniqueConstraintViolationException $e) {
            throw new AccountAlreadyExistsException(
                message: 'Email address is already associated with another account.',
                previous: $e,
            );
        }
    }

    /**
     * @throws AccountNotFoundException
     */
    #[Override]
    public function delete(string $uuid): void
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->delete()->from(from: Account::class, alias: 'account');
        $builder->andWhere($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);

        if (!$builder->getQuery()->execute()) {
            throw new AccountNotFoundException(
                message: 'Account with provided identifier not found.',
            );
        }
    }
}
