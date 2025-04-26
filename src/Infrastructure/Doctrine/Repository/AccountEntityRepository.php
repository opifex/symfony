<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Contract\AccountEntityBuilderInterface;
use App\Domain\Contract\AccountEntityInterface;
use App\Domain\Contract\AccountEntityRepositoryInterface;
use App\Domain\Exception\AccountNotFoundException;
use App\Domain\Model\Account;
use App\Domain\Model\AccountSearchCriteria;
use App\Domain\Model\AccountSearchResult;
use App\Infrastructure\Doctrine\Mapping\Default\AccountEntity;
use App\Infrastructure\Doctrine\Mapping\Default\AccountEntityBuilder;
use App\Infrastructure\Doctrine\Mapping\Default\AccountEntityMapper;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Override;
use Traversable;

final class AccountEntityRepository implements AccountEntityRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $defaultEntityManager,
    ) {
    }

    public function createAccountEntityBuilder(): AccountEntityBuilderInterface
    {
        return new AccountEntityBuilder();
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
            $builder->setParameter(key: 'email', value: '%' . $criteria->getEmail() . '%', type: ParameterType::STRING);
        }

        if (!is_null($criteria->getStatus())) {
            $builder->andWhere($builder->expr()->eq(x: 'account.status', y: ':status'));
            $builder->setParameter(key: 'status', value: $criteria->getStatus(), type: ParameterType::STRING);
        }

        $builder->addOrderBy($builder->expr()->desc(expr: 'account.createdAt'));

        $builder->setFirstResult($criteria->getPagination()?->getOffset());
        $builder->setMaxResults($criteria->getPagination()?->getLimit());

        $paginator = new Paginator($builder);
        /** @var Traversable<int, AccountEntity> $iterator */
        $iterator = $paginator->getIterator();

        $this->defaultEntityManager->clear();

        return new AccountSearchResult(AccountEntityMapper::mapMany(...$iterator), $paginator->count());
    }

    #[Override]
    public function findOneByEmail(string $email): Account
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['account'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->setParameter(key: 'email', value: $email, type: ParameterType::STRING);

        try {
            /** @var AccountEntity $account */
            $account = $builder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw AccountNotFoundException::create($e);
        }

        $this->defaultEntityManager->clear();

        return AccountEntityMapper::mapOne($account);
    }

    #[Override]
    public function findOneByUuid(string $uuid): Account
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['account'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: ParameterType::STRING);

        try {
            /** @var AccountEntity $account */
            $account = $builder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw AccountNotFoundException::create($e);
        }

        $this->defaultEntityManager->clear();

        return AccountEntityMapper::mapOne($account);
    }

    #[Override]
    public function findStatusByUuid(string $uuid): string
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['account.status'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: ParameterType::STRING);

        try {
            $status = (string) $builder->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            throw AccountNotFoundException::create($e);
        }

        $this->defaultEntityManager->clear();

        return $status;
    }

    #[Override]
    public function updateEmailByUuid(string $uuid, string $email): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
        $builder->set(key: 'account.email', value: ':email');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: ParameterType::STRING);
        $builder->setParameter(key: 'email', value: $email, type: ParameterType::STRING);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
        }

        $this->defaultEntityManager->clear();
    }

    #[Override]
    public function updateLocaleByUuid(string $uuid, string $locale): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
        $builder->set(key: 'account.locale', value: ':locale');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: ParameterType::STRING);
        $builder->setParameter(key: 'locale', value: $locale, type: ParameterType::STRING);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
        }

        $this->defaultEntityManager->clear();
    }

    #[Override]
    public function updateStatusByUuid(string $uuid, string $status): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
        $builder->set(key: 'account.status', value: ':status');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: ParameterType::STRING);
        $builder->setParameter(key: 'status', value: $status, type: ParameterType::STRING);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
        }

        $this->defaultEntityManager->clear();
    }

    #[Override]
    public function updatePasswordByUuid(string $uuid, string $password): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
        $builder->set(key: 'account.password', value: ':password');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: ParameterType::STRING);
        $builder->setParameter(key: 'password', value: $password, type: ParameterType::STRING);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
        }

        $this->defaultEntityManager->clear();
    }

    #[Override]
    public function addOneAccount(AccountEntityInterface $accountEntity): string
    {
        /** @var AccountEntity $accountEntity */
        $this->defaultEntityManager->persist($accountEntity);
        $this->defaultEntityManager->flush();
        $this->defaultEntityManager->clear();

        return $accountEntity->uuid;
    }

    #[Override]
    public function deleteOneByUuid(string $uuid): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->delete()->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid, type: ParameterType::STRING);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
        }

        $this->defaultEntityManager->clear();
    }

    #[Override]
    public function checkExistsByEmail(string $email): bool
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['1'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->setParameter(key: 'email', value: $email, type: ParameterType::STRING);

        $exists = (bool) $builder->getQuery()->getOneOrNullResult();

        $this->defaultEntityManager->clear();

        return $exists;
    }
}
