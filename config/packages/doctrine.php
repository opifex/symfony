<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'doctrine', config: [
        'dbal' => [
            'default_connection' => 'default',
            'connections' => [
                'default' => [
                    'url' => '%env(resolve:DATABASE_URL)%',
                ],
            ],
        ],
        'orm' => [
            'auto_generate_proxy_classes' => true,
            'default_entity_manager' => 'default',
            'enable_lazy_ghost_objects' => true,
            'entity_managers' => [
                'default' => [
                    'connection' => 'default',
                    'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                    'mappings' => [
                        'default' => [
                            'dir' => '%kernel.project_dir%/src/Infrastructure/Doctrine/Mapping',
                            'prefix' => 'App\Infrastructure\Doctrine\Mapping',
                            'type' => 'attribute',
                        ],
                    ],
                ],
            ],
        ],
    ]);

    if ($configurator->env() === 'test') {
        $configurator->extension(
            namespace: 'doctrine',
            config: [
                'dbal' => [
                    'dbname_suffix' => '_test%env(default::TEST_TOKEN)%',
                ],
            ],
        );
    }

    if ($configurator->env() === 'prod') {
        $configurator->extension(
            namespace: 'doctrine',
            config: [
                'orm' => [
                    'auto_generate_proxy_classes' => false,
                    'metadata_cache_driver' => [
                        'type' => 'pool',
                        'pool' => 'cache.doctrine',
                    ],
                    'query_cache_driver' => [
                        'type' => 'pool',
                        'pool' => 'cache.doctrine',
                    ],
                    'result_cache_driver' => [
                        'type' => 'pool',
                        'pool' => 'cache.doctrine',
                    ],
                    'second_level_cache' => [
                        'enabled' => true,
                        'region_cache_driver' => [
                            'type' => 'pool',
                            'pool' => 'cache.doctrine',
                        ],
                    ],
                ],
            ],
        );
    }
};
