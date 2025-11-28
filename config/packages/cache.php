<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'framework' => [
        'cache' => [
            'prefix_seed' => '%env(APP_NAME)%',
            'default_redis_provider' => '%env(REDIS_DSN)%',
            'pools' => [
                [
                    'name' => 'cache.doctrine',
                    'default_lifetime' => 60,
                    'adapters' => [
                        'cache.adapter.redis',
                        'cache.adapter.array',
                    ],
                ],
                [
                    'name' => 'cache.storage',
                    'default_lifetime' => 60,
                    'adapters' => [
                        'cache.adapter.redis',
                        'cache.adapter.array',
                    ],
                ],
                [
                    'name' => 'cache.rate_limiter',
                    'default_lifetime' => 60,
                    'adapters' => [
                        'cache.adapter.redis',
                        'cache.adapter.array',
                    ],
                ],
            ],
        ],
    ],
]);
