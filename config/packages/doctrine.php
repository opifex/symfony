<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;

return static function (ContainerConfigurator $configurator, DoctrineConfig $doctrine): void {
    $doctrine->dbal()->defaultConnection(value: 'default');

    $defaultConnection = $doctrine->dbal()->connection(name: 'default');
    $defaultConnection->url(value: '%env(resolve:DATABASE_URL)%');

    if ($configurator->env() === 'test') {
        $defaultConnection->dbnameSuffix(value: '_test%env(default::TEST_TOKEN)%');
    }

    $doctrine->orm()->autoGenerateProxyClasses(value: true);
    $doctrine->orm()->defaultEntityManager(value: 'default');
    $doctrine->orm()->enableLazyGhostObjects(value: true);

    $defaultEntityManager = $doctrine->orm()->entityManager(name: 'default')
        ->connection(value: 'default')
        ->namingStrategy(value: 'doctrine.orm.naming_strategy.underscore_number_aware');

    $defaultEntityManager->mapping(name: 'default')
        ->dir(value: '%kernel.project_dir%/src/Infrastructure/Doctrine/Mapping')
        ->prefix(value: 'App\Infrastructure\Doctrine\Mapping')
        ->type(value: 'attribute');

    if ($configurator->env() === 'prod') {
        $doctrine->orm()->autoGenerateProxyClasses(value: false);

        $defaultEntityManager->metadataCacheDriver()->type(value: 'pool')->pool(value: 'cache.doctrine');
        $defaultEntityManager->queryCacheDriver()->type(value: 'pool')->pool(value: 'cache.doctrine');
        $defaultEntityManager->resultCacheDriver()->type(value: 'pool')->pool(value: 'cache.doctrine');
        $defaultEntityManager->secondLevelCache()->enabled(value: true)->regionCacheDriver()->type(value: 'pool')->pool(value: 'cache.doctrine');
    }
};
