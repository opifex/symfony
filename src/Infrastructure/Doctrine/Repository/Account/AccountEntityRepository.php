<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\Account;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Model\Account;
use App\Domain\Model\AccountIdentifier;
use App\Domain\Model\AccountSearchCriteria;
use App\Domain\Model\AccountSearchResult;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Override;
use Symfony\Component\Uid\Uuid;
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

        $paginator = new Paginator($builder);
        /** @var Traversable<int, AccountEntity> $iterator */
        $iterator = $paginator->getIterator();

        $this->defaultEntityManager->clear();

        return new AccountSearchResult(AccountEntityMapper::mapAll(...$iterator), $paginator->count());
    }

    #[Override]
    public function findOneById(string $id): ?Account
    {
        $accountRepository = $this->defaultEntityManager->getRepository(AccountEntity::class);
        $accountEntity = $accountRepository->findOneBy(criteria: [
            'id' => Uuid::fromString($id)->toString(),
        ]);

        $this->defaultEntityManager->clear();

        return $accountEntity ? AccountEntityMapper::map($accountEntity) : null;
    }

    #[Override]
    public function findOneByEmail(string $email): ?Account
    {
        $accountRepository = $this->defaultEntityManager->getRepository(AccountEntity::class);
        $accountEntity = $accountRepository->findOneBy(criteria: ['email' => $email]);

        $this->defaultEntityManager->clear();

        return $accountEntity ? AccountEntityMapper::map($accountEntity) : null;
    }

    #[Override]
    public function delete(Account $account): void
    {
        $builder = $this->defaultEntityManager->createQueryBuilder();
        $builder->delete()->from(from: AccountEntity::class, alias: 'account');
        $builder->where($builder->expr()->eq(x: 'account.id', y: ':id'));
        $builder->setParameter(key: 'id', value: Uuid::fromString($account->getId()->toString())->toString());

        $builder->getQuery()->execute();

        $this->defaultEntityManager->clear();
    }

    #[Override]
    public function save(Account $account): AccountIdentifier
    {
        $accountRepository = $this->defaultEntityManager->getRepository(AccountEntity::class);
        $accountEntity = $accountRepository->findOneBy(criteria: [
            'id' => Uuid::fromString($account->getId()->toString())->toString(),
        ]);

        if ($accountEntity === null) {
            $accountEntity = new AccountEntity();
            $accountEntity->id = Uuid::fromString($account->getId()->toString())->toString();
            $accountEntity->createdAt = $account->getCreatedAt()->toImmutable();
        }

        $accountEntity->email = $account->getEmail()->toString();
        $accountEntity->locale = $account->getLocale()->toString();
        $accountEntity->password = $account->getPassword()->toString();
        $accountEntity->roles = $account->getRoles();
        $accountEntity->status = $account->getStatus()->toString();

        $this->defaultEntityManager->persist($accountEntity);

        $this->defaultEntityManager->flush();
        $this->defaultEntityManager->clear();

        return AccountIdentifier::fromString((string) $accountEntity->id);
    }
}
