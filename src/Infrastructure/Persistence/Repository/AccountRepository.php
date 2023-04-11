<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Contract\Repository\AccountRepositoryInterface;
use App\Domain\Entity\Account\Account;
use App\Domain\Exception\AccountNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class AccountRepository implements AccountRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function findByCriteria(array $criteria, int $limit, int $offset): iterable
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(select: 'account');
        $builder->from(from: Account::class, alias: 'account');

        if (isset($criteria['email']) && is_scalar($criteria['email'])) {
            $builder->andWhere($builder->expr()->like(x: 'account.email', y: ':email'));
            $builder->setParameter(key: 'email', value: '%' . $criteria['email'] . '%');
        }

        $builder->orderBy(sort: 'account.createdAt', order: 'ASC');

        $paginator = new Paginator($builder);
        $paginator->getQuery()->setFirstResult($offset)->setMaxResults($limit);

        return $paginator;
    }

    /**
     * @throws AccountNotFoundException
     * @throws NonUniqueResultException
     * @throws UnexpectedResultException
     */
    public function findOneByEmail(string $email): Account
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(select: 'account');
        $builder->from(from: Account::class, alias: 'account');
        $builder->andWhere($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->setParameter(key: 'email', value: $email);

        try {
            $entity = $builder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw new AccountNotFoundException($e->getMessage(), previous: $e);
        }

        return $entity instanceof Account ? $entity : throw new UnexpectedResultException();
    }

    /**
     * @throws AccountNotFoundException
     * @throws NonUniqueResultException
     * @throws UnexpectedResultException
     */
    public function findOneByUuid(string $uuid): Account
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(select: 'account');
        $builder->from(from: Account::class, alias: 'account');
        $builder->andWhere($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid);

        try {
            $entity = $builder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw new AccountNotFoundException($e->getMessage(), previous: $e);
        }

        return $entity instanceof Account ? $entity : throw new UnexpectedResultException();
    }

    public function persist(Account $account): void
    {
        $this->entityManager->persist($account);
        $this->entityManager->flush();
    }

    public function remove(Account $account): void
    {
        $this->entityManager->remove($account);
        $this->entityManager->flush();
    }
}
