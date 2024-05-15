<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

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
use SensitiveParameter;
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

        $accountCollection = new AccountCollection(new Paginator($builder));
        $this->entityManager->clear();

        return $accountCollection;
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Override]
    public function findOneByEmail(string $email): Account
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(select: 'account')->from(from: Account::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->setParameter(key: 'email', value: $email, type: Types::STRING);

        try {
            /** @var Account $account */
            $account = $builder->getQuery()->getSingleResult();
            $this->entityManager->clear();

            return $account;
        } catch (NoResultException $e) {
            throw new AccountNotFoundException(
                message: 'Account with provided identifier not found.',
                previous: $e,
            );
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Override]
    public function findOneByUuid(string $uuid): Account
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(select: 'account')->from(from: Account::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);

        try {
            /** @var Account $account */
            $account = $builder->getQuery()->getSingleResult();
            $this->entityManager->clear();

            return $account;
        } catch (NoResultException $e) {
            throw new AccountNotFoundException(
                message: 'Account with provided identifier not found.',
                previous: $e,
            );
        }
    }

    #[Override]
    public function updateEmailByUuid(string $uuid, string $email): void
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->update(update: Account::class, alias: 'account');
        $builder->set(key: 'account.email', value: ':email');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);
        $builder->setParameter(key: 'email', value: $email, type: Types::STRING);

        if (!$builder->getQuery()->execute()) {
            throw new AccountNotFoundException(
                message: 'Account with provided identifier not found.',
            );
        }
    }

    #[Override]
    public function updatePasswordByUuid(string $uuid, #[SensitiveParameter] string $password): void
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->update(update: Account::class, alias: 'account');
        $builder->set(key: 'account.password', value: ':password');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);
        $builder->setParameter(key: 'password', value: $password, type: Types::STRING);

        if (!$builder->getQuery()->execute()) {
            throw new AccountNotFoundException(
                message: 'Account with provided identifier not found.',
            );
        }
    }

    #[Override]
    public function updateStatusByUuid(string $uuid, string $status): void
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->update(update: Account::class, alias: 'account');
        $builder->set(key: 'account.status', value: ':status');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);
        $builder->setParameter(key: 'status', value: $status, type: Types::STRING);

        if (!$builder->getQuery()->execute()) {
            throw new AccountNotFoundException(
                message: 'Account with provided identifier not found.',
            );
        }
    }

    #[Override]
    public function updateRolesByUuid(string $uuid, array $roles): void
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->update(update: Account::class, alias: 'account');
        $builder->set(key: 'account.roles', value: ':roles');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);
        $builder->setParameter(key: 'roles', value: $roles, type: Types::JSON);

        if (!$builder->getQuery()->execute()) {
            throw new AccountNotFoundException(
                message: 'Account with provided identifier not found.',
            );
        }
    }

    #[Override]
    public function updateLocaleByUuid(string $uuid, string $locale): void
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->update(update: Account::class, alias: 'account');
        $builder->set(key: 'account.locale', value: ':locale');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);
        $builder->setParameter(key: 'locale', value: $locale, type: Types::STRING);

        if (!$builder->getQuery()->execute()) {
            throw new AccountNotFoundException(
                message: 'Account with provided identifier not found.',
            );
        }
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function addOneAccount(Account $account): void
    {
        try {
            $this->addOneEntity($account);
        } catch (UniqueConstraintViolationException $e) {
            throw new AccountAlreadyExistsException(
                message: 'Email address is already associated with another account.',
                previous: $e,
            );
        }
    }

    #[Override]
    public function deleteOneByUuid(string $uuid): void
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->delete()->from(from: Account::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);

        if (!$builder->getQuery()->execute()) {
            throw new AccountNotFoundException(
                message: 'Account with provided identifier not found.',
            );
        }
    }
}
