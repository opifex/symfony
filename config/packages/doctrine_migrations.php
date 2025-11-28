<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

// todo: validate configuration
return App::config([
    'doctrine_migrations' => [
        'all_or_nothing' => true,
        'transactional' => true,
        'enable_profiler' => '%kernel.debug%',
        'custom_template' => '%kernel.project_dir%/src/Presentation/Resource/Template/bundles/DoctrineMigrationsBundle/migration.php.twig',
        'migrations_paths' => [
            'App\Infrastructure\Doctrine\Migration' => '%kernel.project_dir%/src/Infrastructure/Doctrine/Migration',
        ],
        'storage' => [
            'table_storage' => [
                'table_name' => 'migration',
            ],
        ],
    ],
]);

// return static function (DoctrineMigrationsConfig $doctrineMigrations): void {
//     $doctrineMigrations->allOrNothing(value: true);
//     $doctrineMigrations->transactional(value: true);
//     $doctrineMigrations->enableProfiler(value: '%kernel.debug%');
//     $doctrineMigrations->customTemplate(
//         value: '%kernel.project_dir%/src/Presentation/Resource/Template/bundles/DoctrineMigrationsBundle/migration.php.twig',
//     );
//     $doctrineMigrations->migrationsPath(
//         namespace: 'App\Infrastructure\Doctrine\Migration',
//         value: '%kernel.project_dir%/src/Infrastructure/Doctrine/Migration',
//     );
//     $doctrineMigrations->storage()->tableStorage()->tableName(value: 'migration');
// };
