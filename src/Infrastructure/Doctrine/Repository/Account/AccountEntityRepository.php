<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\Account;

use App\Domain\Contract\Account\AccountEntityInterface;
use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\Account;
use App\Domain\Model\AccountSearchCriteria;
use App\Domain\Model\AccountSearchResult;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use LogicException;
use Override;
use Traversable;

final class AccountEntityRepository implements AccountEntityRepositoryInterface
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

        return new AccountSearchResult(AccountEntityMapper::mapAll(...$iterator), $paginator->count());
    }

    #[Override]
    public function findOneById(string $id): Account
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['account'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.id', y: ':id'));
        $builder->setParameter(key: 'id', value: $id, type: ParameterType::STRING);

        try {
            /** @var AccountEntity $account */
            $account = $builder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw AccountNotFoundException::create($e);
        }

        $this->defaultEntityManager->clear();

        return AccountEntityMapper::map($account);
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

        return AccountEntityMapper::map($account);
    }

    #[Override]
    public function getStatusById(string $id): string
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['account.status'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.id', y: ':id'));
        $builder->setParameter(key: 'id', value: $id, type: ParameterType::STRING);

        try {
            $status = (string) $builder->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            throw AccountNotFoundException::create($e);
        }

        $this->defaultEntityManager->clear();

        return $status;
    }

    #[Override]
    public function updateEmailById(string $id, string $email): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
        $builder->set(key: 'account.email', value: ':email');
        $builder->where($builder->expr()->eq(x: 'account.id', y: ':id'));
        $builder->setParameter(key: 'id', value: $id, type: ParameterType::STRING);
        $builder->setParameter(key: 'email', value: $email, type: ParameterType::STRING);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
        }

        $this->defaultEntityManager->clear();
    }

    #[Override]
    public function updateLocaleById(string $id, string $locale): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
        $builder->set(key: 'account.locale', value: ':locale');
        $builder->where($builder->expr()->eq(x: 'account.id', y: ':id'));
        $builder->setParameter(key: 'id', value: $id, type: ParameterType::STRING);
        $builder->setParameter(key: 'locale', value: $locale, type: ParameterType::STRING);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
        }

        $this->defaultEntityManager->clear();
    }

    #[Override]
    public function updateStatusById(string $id, string $status): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
        $builder->set(key: 'account.status', value: ':status');
        $builder->where($builder->expr()->eq(x: 'account.id', y: ':id'));
        $builder->setParameter(key: 'id', value: $id, type: ParameterType::STRING);
        $builder->setParameter(key: 'status', value: $status, type: ParameterType::STRING);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
        }

        $this->defaultEntityManager->clear();
    }

    #[Override]
    public function updatePasswordById(string $id, string $password): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
        $builder->set(key: 'account.password', value: ':password');
        $builder->where($builder->expr()->eq(x: 'account.id', y: ':id'));
        $builder->setParameter(key: 'id', value: $id, type: ParameterType::STRING);
        $builder->setParameter(key: 'password', value: $password, type: ParameterType::STRING);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
        }

        $this->defaultEntityManager->clear();
    }

    #[Override]
    public function deleteById(string $id): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->delete()->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.id', y: ':id'));
        $builder->setParameter(key: 'id', value: $id, type: ParameterType::STRING);

        if (!$builder->getQuery()->execute()) {
            throw AccountNotFoundException::create();
        }

        $this->defaultEntityManager->clear();
    }

    #[Override]
    public function checkEmailExists(string $email): bool
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['1'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->setParameter(key: 'email', value: $email, type: ParameterType::STRING);

        $exists = (bool) $builder->getQuery()->getOneOrNullResult();

        $this->defaultEntityManager->clear();

        return $exists;
    }

    #[Override]
    public function save(AccountEntityInterface $entity): string
    {
        /** @var AccountEntity $entity */
        $this->defaultEntityManager->persist($entity);
        $this->defaultEntityManager->flush();
        $this->defaultEntityManager->clear();

        return $entity->id ?? throw new LogicException(message: 'Failed to generate identifier during persistence.');
    }
}
