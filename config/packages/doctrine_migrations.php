<?php

declare(strict_types=1);

use Symfony\Config\DoctrineMigrationsConfig;

return static function (DoctrineMigrationsConfig $doctrineMigrations): void {
    $doctrineMigrations->allOrNothing(value: true);
    $doctrineMigrations->transactional(value: true);
    $doctrineMigrations->enableProfiler(value: '%kernel.debug%');
    $doctrineMigrations->customTemplate(value: '%kernel.project_dir%/src/Presentation/Resource/Template/bundles/DoctrineMigrationsBundle/migration.php.twig');
    $doctrineMigrations->migrationsPath(namespace: 'App\Infrastructure\Doctrine\Migration', value: '%kernel.project_dir%/src/Infrastructure/Doctrine/Migration');
    $doctrineMigrations->storage()->tableStorage()->tableName(value: 'migration');
};
