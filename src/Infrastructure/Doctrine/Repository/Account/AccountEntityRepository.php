<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\Account;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Model\Account;
use App\Domain\Model\AccountSearchCriteria;
use App\Domain\Model\AccountSearchResult;
use App\Infrastructure\Doctrine\Mapping\AccountEntity;
use Doctrine\ORM\EntityManagerInterface;
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
        $accountRepository = $this->defaultEntityManager->getRepository(AccountEntity::class);
        $builder = $accountRepository->createQueryBuilder(alias: 'account')->select();

        if (!is_null($criteria->email)) {
            $builder->andWhere($builder->expr()->like(x: 'account.email', y: ':email'));
            $builder->setParameter(key: 'email', value: '%' . $criteria->email . '%');
        }

        if (!is_null($criteria->status)) {
            $builder->andWhere($builder->expr()->eq(x: 'account.status', y: ':status'));
            $builder->setParameter(key: 'status', value: $criteria->status);
        }

        $builder->addOrderBy($builder->expr()->desc(expr: 'account.createdAt'));

        $builder->setFirstResult($criteria->pagination?->offset());
        $builder->setMaxResults($criteria->pagination?->limit);

        $paginator = new Paginator($builder);
        /** @var Traversable<int, AccountEntity> $iterator */
        $iterator = $paginator->getIterator();

        $this->defaultEntityManager->clear();

        return new AccountSearchResult(AccountEntityMapper::mapAll(...$iterator), $paginator->count());
    }

    #[Override]
    public function findOneByUuid(string $uuid): ?Account
    {
        $accountRepository = $this->defaultEntityManager->getRepository(AccountEntity::class);
        $accountEntity = $accountRepository->findOneBy(criteria: ['uuid' => $uuid]);

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
        $accountRepository = $this->defaultEntityManager->getRepository(AccountEntity::class);
        $builder = $accountRepository->createQueryBuilder(alias: 'account')->delete();
        $builder->where($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $account->id);

        $builder->getQuery()->execute();

        $this->defaultEntityManager->clear();
    }

    #[Override]
    public function save(Account $account): string
    {
        $accountRepository = $this->defaultEntityManager->getRepository(AccountEntity::class);
        $accountEntity = $accountRepository->findOneBy(criteria: ['uuid' => $account->id]);

        $accountEntity ??= new AccountEntity();
        $accountEntity->email = $account->email;
        $accountEntity->locale = $account->locale;
        $accountEntity->password = $account->password;
        $accountEntity->roles = $account->roles;
        $accountEntity->status = $account->status;

        $this->defaultEntityManager->persist($accountEntity);

        $this->defaultEntityManager->flush();
        $this->defaultEntityManager->clear();

        return $accountEntity->uuid ?? throw new LogicException(message: 'Missing UUID after persisting.');
    }
}
