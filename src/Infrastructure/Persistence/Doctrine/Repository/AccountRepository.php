<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountCollection;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\SortingOrder;
use App\Domain\Exception\AccountAlreadyExistsException;
use App\Domain\Exception\AccountNotFoundException;
use App\Infrastructure\Persistence\Doctrine\Mapping\Default\AccountEntity;
use App\Infrastructure\Persistence\Doctrine\Mapping\Default\AccountMapper;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use LogicException;
use Override;
use SensitiveParameter;

final class AccountRepository implements AccountRepositoryInterface
{
    public function __construct(protected EntityManagerInterface $defaultEntityManager)
    {
    }

    #[Override]
    public function findByCriteria(AccountSearchCriteria $criteria): AccountCollection
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(select: 'account')->from(from: AccountEntity::class, alias: 'account');

        if (!is_null($criteria->email)) {
            $builder->andWhere($builder->expr()->like(x: 'account.email', y: ':email'));
            $builder->setParameter(key: 'email', value: '%' . $criteria->email . '%', type: Types::STRING);
        }

        if (!is_null($criteria->status)) {
            $builder->andWhere($builder->expr()->eq(x: 'account.status', y: ':status'));
            $builder->setParameter(key: 'status', value: $criteria->status, type: Types::STRING);
        }

        if (!is_null($criteria->sorting)) {
            $orderBy = match ($criteria->sorting->order) {
                SortingOrder::Asc => $builder->expr()->asc(...),
                SortingOrder::Desc => $builder->expr()->desc(...),
            };

            $expression = match ($criteria->sorting->field) {
                AccountSearchCriteria::FIELD_CREATED_AT => 'account.createdAt',
                AccountSearchCriteria::FIELD_EMAIL => 'account.email',
                AccountSearchCriteria::FIELD_STATUS => 'account.status',
                default => throw new LogicException(
                    message: sprintf('Sorting field "%s" is not supported.', $criteria->sorting->field),
                ),
            };

            $builder->addOrderBy($orderBy($expression));
        }

        $builder->setFirstResult($criteria->pagination?->offset);
        $builder->setMaxResults($criteria->pagination?->limit);

        return AccountMapper::mapMany(new Paginator($builder));
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Override]
    public function findOneByEmail(string $email): Account
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(select: 'account')->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->setParameter(key: 'email', value: $email, type: Types::STRING);

        try {
            /** @var AccountEntity $account */
            $account = $builder->getQuery()->getSingleResult();

            return AccountMapper::mapOne($account);
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
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(select: 'account')->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);

        try {
            /** @var AccountEntity $account */
            $account = $builder->getQuery()->getSingleResult();

            return AccountMapper::mapOne($account);
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
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
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
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
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
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
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
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
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
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
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
            $entity = new AccountEntity(
                uuid: $account->getUuid(),
                createdAt: $account->getCreatedAt(),
                email: $account->getEmail(),
                password: $account->getPassword(),
                locale: $account->getLocale(),
                roles: $account->getRoles(),
                status: $account->getStatus(),
            );

            $classMetadata = $this->defaultEntityManager->getClassMetadata($entity::class);
            $convertToDatabaseValue = $this->defaultEntityManager->getConnection()->convertToDatabaseValue(...);
            $tableFields = [];

            foreach ($classMetadata->getFieldNames() as $fieldName) {
                $tableFields[$classMetadata->getColumnName($fieldName)] = $convertToDatabaseValue(
                    value: $classMetadata->getFieldValue($entity, $fieldName),
                    type: $classMetadata->getTypeOfField($fieldName) ?? '',
                );
            }

            $this->defaultEntityManager->getConnection()->insert($classMetadata->getTableName(), $tableFields);
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
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->delete()->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);

        if (!$builder->getQuery()->execute()) {
            throw new AccountNotFoundException(
                message: 'Account with provided identifier not found.',
            );
        }
    }
}
