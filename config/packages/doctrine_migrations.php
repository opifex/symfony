<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'doctrine_migrations', config: [
        'all_or_nothing' => true,
        'custom_template' => '%kernel.project_dir%/src/Presentation/Resource/Template/bundles/DoctrineMigrationsBundle/migration.php.twig',
        'enable_profiler' => '%kernel.debug%',
        'migrations_paths' => [
            'App\Infrastructure\Doctrine\Migration' => '%kernel.project_dir%/src/Infrastructure/Doctrine/Migration',
        ],
        'storage' => [
            'table_storage' => [
                'table_name' => 'migration',
            ],
        ],
        'transactional' => true,
    ]);
};
