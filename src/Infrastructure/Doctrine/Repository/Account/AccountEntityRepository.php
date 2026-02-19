<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\Account;

use App\Domain\Account\Account;
use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\AccountSearchCriteria;
use App\Domain\Account\AccountSearchResult;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use App\Domain\Account\Exception\AccountNotFoundException;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @throws Exception
     */
    #[Override]
    public function findByCriteria(AccountSearchCriteria $criteria): AccountSearchResult
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['account'])->from(from: AccountEntity::class, alias: 'account');

        if ($criteria->getEmail() !== null) {
            $builder->andWhere($builder->expr()->like(x: 'account.email', y: ':email'));
            $builder->setParameter(key: 'email', value: '%' . $criteria->getEmail() . '%');
        }

        if ($criteria->getStatus() !== null) {
            $builder->andWhere($builder->expr()->eq(x: 'account.status', y: ':status'));
            $builder->setParameter(key: 'status', value: $criteria->getStatus());
        }

        $builder->addOrderBy($builder->expr()->desc(expr: 'account.createdAt'));

        $builder->setFirstResult($criteria->getPagination()?->getOffset());
        $builder->setMaxResults($criteria->getPagination()?->getLimit());

        $paginator = new Paginator($builder, fetchJoinCollection: false);
        /** @var Traversable<int, AccountEntity> $iterator */
        $iterator = $paginator->getIterator();

        foreach ($iterator as $accountEntity) {
            $this->defaultEntityManager->detach($accountEntity);
        }

        return new AccountSearchResult(
            accounts: AccountEntityMapper::mapAll(...$iterator),
            totalResultCount: $paginator->count(),
        );
    }

    #[Override]
    public function findOneById(AccountIdentifier $id): Account
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['account'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.id', y: ':id'));
        $builder->setParameter(key: 'id', value: $id->toString());
        $accountEntity = $builder->getQuery()->getOneOrNullResult();

        if (!$accountEntity instanceof AccountEntity) {
            throw AccountNotFoundException::create();
        }

        $this->defaultEntityManager->detach($accountEntity);

        return AccountEntityMapper::map($accountEntity);
    }

    #[Override]
    public function findOneByEmail(EmailAddress $email): Account
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['account'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->setParameter(key: 'email', value: $email->toString());
        $accountEntity = $builder->getQuery()->getOneOrNullResult();

        if (!$accountEntity instanceof AccountEntity) {
            throw AccountNotFoundException::create();
        }

        $this->defaultEntityManager->detach($accountEntity);

        return AccountEntityMapper::map($accountEntity);
    }

    #[Override]
    public function ensureEmailIsAvailable(EmailAddress $email): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->select(['1'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->setParameter(key: 'email', value: $email->toString());
        $builder->setMaxResults(maxResults: 1);

        if ($builder->getQuery()->getOneOrNullResult() !== null) {
            throw AccountAlreadyExistsException::create();
        }
    }

    #[Override]
    public function delete(Account $account): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->delete()->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.id', y: ':id'));
        $builder->setParameter(key: 'id', value: $account->getId()->toString());
        $builder->getQuery()->execute();
    }

    #[Override]
    public function save(Account $account): Account
    {
        $accountRepository = $this->defaultEntityManager->getRepository(AccountEntity::class);
        $accountEntity = $accountRepository->findOneBy(criteria: ['id' => $account->getId()->toString()]);

        if ($accountEntity === null) {
            $accountEntity = new AccountEntity();
            $accountEntity->id = $account->getId()->toString();
            $accountEntity->createdAt = $account->getCreatedAt()->toImmutable();
        }

        $accountEntity->email = $account->getEmail()->toString();
        $accountEntity->locale = $account->getLocale()->toString();
        $accountEntity->password = $account->getPassword()->toString();
        $accountEntity->roles = $account->getRoles()->toArray();
        $accountEntity->status = $account->getStatus()->toString();

        $this->defaultEntityManager->persist($accountEntity);
        $this->defaultEntityManager->flush();
        $this->defaultEntityManager->detach($accountEntity);

        return AccountEntityMapper::map($accountEntity);
    }
}
