<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\AccountSearchResult;
use App\Domain\Exception\AccountNotFoundException;
use App\Infrastructure\Doctrine\Mapping\Default\AccountEntity;
use App\Infrastructure\Doctrine\Mapping\Default\AccountFactory;
use App\Infrastructure\Doctrine\Mapping\Default\AccountMapper;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
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

        return new AccountSearchResult(AccountMapper::mapMany(...$iterator), $paginator->count());
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

        return AccountMapper::mapOne($account);
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

        return AccountMapper::mapOne($account);
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
    public function updatePasswordByUuid(string $uuid, #[SensitiveParameter] string $password): void
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
    public function addOneAccount(string $email, #[SensitiveParameter] string $password): string
    {
        $entity = AccountFactory::createEntity($email, $password);
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
