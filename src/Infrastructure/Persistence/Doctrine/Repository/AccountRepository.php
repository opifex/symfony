<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\AccountSearchResult;
use App\Domain\Entity\AccountStatus;
use App\Domain\Entity\SortingOrder;
use App\Domain\Exception\AccountNotFoundException;
use App\Infrastructure\Persistence\Doctrine\Mapping\Default\AccountEntity;
use App\Infrastructure\Persistence\Doctrine\Mapping\Default\AccountFactory;
use App\Infrastructure\Persistence\Doctrine\Mapping\Default\AccountMapper;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use LogicException;
use Override;
use SensitiveParameter;
use Traversable;

final class AccountRepository implements AccountRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $defaultEntityManager,
    ) {
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function findByCriteria(AccountSearchCriteria $criteria): AccountSearchResult
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['account'])->from(from: AccountEntity::class, alias: 'account');

        if (!is_null($criteria->getEmail())) {
            $builder->andWhere($builder->expr()->like(x: 'account.email', y: ':email'));
            $builder->setParameter(key: 'email', value: '%' . $criteria->getEmail() . '%', type: Types::STRING);
        }

        if (!is_null($criteria->getStatus())) {
            $builder->andWhere($builder->expr()->eq(x: 'account.status', y: ':status'));
            $builder->setParameter(key: 'status', value: $criteria->getStatus(), type: Types::STRING);
        }

        if (!is_null($criteria->getSorting())) {
            $orderBy = match ($criteria->getSorting()->getOrder()) {
                SortingOrder::Asc => $builder->expr()->asc(...),
                SortingOrder::Desc => $builder->expr()->desc(...),
            };

            $expression = match ($criteria->getSorting()->getField()) {
                AccountSearchCriteria::FIELD_CREATED_AT => 'account.createdAt',
                AccountSearchCriteria::FIELD_EMAIL => 'account.email',
                AccountSearchCriteria::FIELD_STATUS => 'account.status',
                default => throw new LogicException(
                    message: sprintf('Sorting field "%s" is not supported.', $criteria->getSorting()->getField()),
                ),
            };

            $builder->addOrderBy($orderBy($expression));
        }

        $builder->setFirstResult($criteria->getPagination()?->getOffset());
        $builder->setMaxResults($criteria->getPagination()?->getLimit());

        $paginator = new Paginator($builder);
        /** @var Traversable<int, AccountEntity> $accountPaginator */
        $accountPaginator = $paginator->getIterator();
        $accounts = AccountMapper::mapMany(...$accountPaginator);

        return new AccountSearchResult($accounts, $paginator->count());
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Override]
    public function findOneByEmail(string $email): Account
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['account'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->setParameter(key: 'email', value: $email, type: Types::STRING);

        try {
            /** @var AccountEntity $account */
            $account = $builder->getQuery()->getSingleResult();

            return AccountMapper::mapOne($account);
        } catch (NoResultException $e) {
            throw AccountNotFoundException::create($e);
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Override]
    public function findOneByUuid(string $uuid): Account
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['account'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);

        try {
            /** @var AccountEntity $account */
            $account = $builder->getQuery()->getSingleResult();

            return AccountMapper::mapOne($account);
        } catch (NoResultException $e) {
            throw AccountNotFoundException::create($e);
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
            throw AccountNotFoundException::create();
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
            throw AccountNotFoundException::create();
        }
    }

    #[Override]
    public function updateStatusByUuid(string $uuid, AccountStatus $status): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
        $builder->set(key: 'account.status', value: ':status');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);
        $builder->setParameter(key: 'status', value: $status->value, type: Types::STRING);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
        }
    }

    #[Override]
    public function updateRolesByUuid(string $uuid, AccountRole ...$role): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
        $builder->set(key: 'account.roles', value: ':roles');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);
        $builder->setParameter(key: 'roles', value: $role, type: Types::JSON);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
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
            throw AccountNotFoundException::create();
        }
    }

    #[Override]
    public function addOneAccount(string $email, #[SensitiveParameter] string $password): string
    {
        $entity = AccountFactory::create($email, $password);
        $this->defaultEntityManager->persist($entity);
        $this->defaultEntityManager->flush();
        $this->defaultEntityManager->clear();

        return $entity->uuid;
    }

    #[Override]
    public function deleteOneByUuid(string $uuid): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->delete()->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: Types::GUID);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
        }
    }
}
