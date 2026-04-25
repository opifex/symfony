<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\Account;

use App\Domain\Account\Account;
use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountAlreadyExistsException;
use App\Domain\Account\Exception\AccountNotFoundException;
use App\Domain\Foundation\SearchResult;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Traversable;

final readonly class AccountEntityRepository implements AccountEntityRepositoryInterface
{
    public function __construct(
        #[Autowire(service: 'doctrine.orm.default_entity_manager')]
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function findByCriteria(
        ?string $accountEmail = null,
        ?string $accountStatus = null,
        ?int $pageNumber = null,
        ?int $pageSize = null,
    ): SearchResult {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(['account'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->isNull(x: 'account.deletedAt'));

        if ($accountEmail !== null) {
            $builder->andWhere($builder->expr()->like(x: 'account.email', y: ':email'));
            $builder->setParameter(key: 'email', value: '%' . $accountEmail . '%');
        }

        if ($accountStatus !== null) {
            $builder->andWhere($builder->expr()->eq(x: 'account.status', y: ':status'));
            $builder->setParameter(key: 'status', value: $accountStatus);
        }

        $builder->addOrderBy($builder->expr()->desc(expr: 'account.createdAt'));

        if ($pageNumber !== null && $pageSize !== null) {
            $builder->setFirstResult(firstResult: ($pageNumber - 1) * $pageSize);
            $builder->setMaxResults($pageSize);
        }

        $paginator = new Paginator($builder, fetchJoinCollection: false);
        /** @var Traversable<int, AccountEntity> $iterator */
        $iterator = $paginator->getIterator();

        foreach ($iterator as $accountEntity) {
            $this->entityManager->detach($accountEntity);
        }

        return new SearchResult(
            items: AccountEntityMapper::mapAll(...$iterator),
            totalCount: $paginator->count(),
            pageNumber: $pageNumber ?? 1,
            pageSize: $pageSize ?? $paginator->count(),
        );
    }

    #[Override]
    public function findOneById(AccountIdentifier $id): Account
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(['account'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.id', y: ':id'));
        $builder->andWhere($builder->expr()->isNull(x: 'account.deletedAt'));
        $builder->setParameter(key: 'id', value: $id->toString());
        $accountEntity = $builder->getQuery()->getOneOrNullResult();

        if (!$accountEntity instanceof AccountEntity) {
            throw AccountNotFoundException::create();
        }

        $this->entityManager->detach($accountEntity);

        return AccountEntityMapper::map($accountEntity);
    }

    #[Override]
    public function findOneByEmail(EmailAddress $email): Account
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(['account'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->andWhere($builder->expr()->isNull(x: 'account.deletedAt'));
        $builder->setParameter(key: 'email', value: $email->toString());
        $accountEntity = $builder->getQuery()->getOneOrNullResult();

        if (!$accountEntity instanceof AccountEntity) {
            throw AccountNotFoundException::create();
        }

        $this->entityManager->detach($accountEntity);

        return AccountEntityMapper::map($accountEntity);
    }

    #[Override]
    public function ensureEmailIsAvailable(EmailAddress $email): void
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(['1'])->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->andWhere($builder->expr()->isNull(x: 'account.deletedAt'));
        $builder->setParameter(key: 'email', value: $email->toString());
        $builder->setMaxResults(maxResults: 1);

        if ($builder->getQuery()->getOneOrNullResult() !== null) {
            throw AccountAlreadyExistsException::create();
        }
    }

    #[Override]
    public function delete(Account $account): void
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->update(update: AccountEntity::class, alias: 'account');
        $builder->set(key: 'account.deletedAt', value: ':now');
        $builder->where($builder->expr()->eq(x: 'account.id', y: ':id'));
        $builder->setParameter(key: 'id', value: $account->id->toString());
        $builder->setParameter(key: 'now', value: new DateTimeImmutable());
        $builder->getQuery()->execute();
    }

    #[Override]
    public function save(Account $account): Account
    {
        $accountRepository = $this->entityManager->getRepository(AccountEntity::class);
        $accountEntity = $accountRepository->findOneBy(criteria: ['id' => $account->id->toString()]);

        if ($accountEntity === null) {
            $accountEntity = new AccountEntity();
            $accountEntity->id = $account->id->toString();
            $accountEntity->createdAt = $account->createdAt->toImmutable();
        }

        $accountEntity->email = $account->email->toString();
        $accountEntity->locale = $account->locale->toString();
        $accountEntity->password = $account->password->toString();
        $accountEntity->roles = $account->roles->toArray();
        $accountEntity->status = $account->status->toString();
        $accountEntity->updatedAt = $account->updatedAt->toImmutable();

        $this->entityManager->persist($accountEntity);
        $this->entityManager->flush();
        $this->entityManager->detach($accountEntity);

        return AccountEntityMapper::map($accountEntity);
    }
}
