<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'framework' => [
        'rate_limiter' => [
            'enabled' => true,
            'limiters' => [
                [
                    'name' => 'authentication',
                    'policy' => 'fixed_window',
                    'limit' => 5,
                    'cache_pool' => 'cache.rate_limiter',
                    'interval' => '1 minute',
                ],
            ],
        ],
    ],
]);
