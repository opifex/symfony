<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'doctrine' => [
        'dbal' => [
            'default_connection' => 'default',
            'connections' => [
                [
                    'name' => 'default',
                    'url' => '%env(resolve:DATABASE_URL)%',
                ],
            ],
        ],
        'orm' => [
            'default_entity_manager' => 'default',
            'entity_managers' => [
                [
                    'name' => 'default',
                    'connection' => 'default',
                    'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                    'mappings' => [
                        [
                            'name' => 'default',
                            'dir' => '%kernel.project_dir%/src/Infrastructure/Doctrine/Mapping',
                            'prefix' => 'App\\Infrastructure\\Doctrine\\Mapping',
                            'type' => 'attribute',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'when@test' => [
        'doctrine' => [
            'dbal' => [
                'connections' => [
                    [
                        'name' => 'default',
                        'dbname_suffix' => '_test%env(default::TEST_TOKEN)%',
                    ],
                ],
            ],
        ],
    ],
    'when@prod' => [
        'doctrine' => [
            'orm' => [
                'entity_managers' => [
                    [
                        'name' => 'default',
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
            ],
        ],
    ],
]);
