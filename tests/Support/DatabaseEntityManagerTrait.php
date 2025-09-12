<?php

declare(strict_types=1);

namespace Tests\Support;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;

trait DatabaseEntityManagerTrait
{
    public static function loadFixtures(array $fixtures = []): void
    {
        $loader = new Loader();
        array_walk($fixtures, fn(string $fixture) => $loader->addFixture(new $fixture()));
        new ORMExecutor(self::getEntityManager(), new ORMPurger())->execute($loader->getFixtures());
    }

    public static function getDatabaseEntity(string $entity, array $criteria = []): object
    {
        return self::getEntityManager()->getRepository($entity)->findOneBy($criteria);
    }

    private static function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(id: 'doctrine')->getManager();
    }
}
