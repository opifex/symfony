<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Contract\Repository\AccountRepositoryInterface;
use App\Domain\Entity\Account\Account;
use App\Domain\Exception\AccountNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class AccountRepository implements AccountRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws MappingException
     */
    public function findByCriteria(array $criteria, array $sort, int $limit, int $offset): iterable
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(select: 'account');
        $builder->from(from: Account::class, alias: 'account');

        $accountMetadata = $this->entityManager->getClassMetadata(Account::class);

        if (isset($criteria['email']) && is_scalar($criteria['email'])) {
            $builder->andWhere($builder->expr()->like(x: 'account.email', y: ':email'));
            $builder->setParameter(key: 'email', value: '%' . $criteria['email'] . '%');
        }

        if (isset($criteria['status']) && is_scalar($criteria['status'])) {
            $builder->andWhere($builder->expr()->eq(x: 'account.status', y: ':status'));
            $builder->setParameter(key: 'status', value: $criteria['status']);
        }

        foreach ($sort as $sortField => $sortOrder) {
            $builder->addOrderBy(
                sort: 'account.' . $accountMetadata->getFieldForColumn($sortField),
                order: $sortOrder === 'desc' ? 'desc' : 'asc',
            );
        }

        if (!$builder->getDQLPart(queryPartName: 'orderBy')) {
            $builder->orderBy(sort: 'account.createdAt', order: 'desc');
        }

        $paginator = new Paginator($builder);
        $paginator->getQuery()->setFirstResult($offset)->setMaxResults($limit);

        return $paginator;
    }

    /**
     * @throws AccountNotFoundException
     * @throws NonUniqueResultException
     */
    public function findOneByEmail(string $email): Account
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(select: 'account');
        $builder->from(from: Account::class, alias: 'account');
        $builder->andWhere($builder->expr()->eq(x: 'account.email', y: ':email'));
        $builder->setParameter(key: 'email', value: $email);

        try {
            /** @var Account */
            return $builder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw new AccountNotFoundException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws AccountNotFoundException
     * @throws NonUniqueResultException
     */
    public function findOneByUuid(string $uuid): Account
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select(select: 'account');
        $builder->from(from: Account::class, alias: 'account');
        $builder->andWhere($builder->expr()->eq(x: 'account.uuid', y: ':uuid'));
        $builder->setParameter(key: 'uuid', value: $uuid);

        try {
            /** @var Account */
            return $builder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw new AccountNotFoundException($e->getMessage(), $e->getCode(), $e);
        }
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
