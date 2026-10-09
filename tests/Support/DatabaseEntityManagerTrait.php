<?php

declare(strict_types=1);

namespace Tests\Support;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

trait DatabaseEntityManagerTrait
{
    public static function loadFixtures(array $fixtures = []): void
    {
        $loader = new Loader();

        foreach ($fixtures as $fixture) {
            $loader->addFixture(new $fixture());
        }

        new ORMExecutor(self::getEntityManager(), new ORMPurger())->execute($loader->getFixtures());
    }

    public static function getDatabaseEntity(string $entity, array $criteria = []): ?object
    {
        return self::getEntityManager()->getRepository($entity)->findOneBy($criteria);
    }

    private static function getEntityManager(): EntityManagerInterface
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = self::getContainer()->get(id: 'doctrine');
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $managerRegistry->getManager();

        return $entityManager;
    }
}
