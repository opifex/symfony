<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'framework', config: [
        'cache' => [
            'prefix_seed' => '%env(APP_NAME)%',
            'default_redis_provider' => '%env(REDIS_DSN)%',
            'pools' => [
                'cache.doctrine' => [
                    'default_lifetime' => 60,
                    'adapters' => [
                        'cache.adapter.redis',
                        'cache.adapter.array',
                    ],
                ],
                'cache.storage' => [
                    'default_lifetime' => 60,
                    'adapters' => [
                        'cache.adapter.redis',
                        'cache.adapter.array',
                    ],
                ],
            ],
        ],
    ]);
};
