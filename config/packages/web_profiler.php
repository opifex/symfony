<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'when@dev' => [
        'framework' => [
            'profiler' => true,
        ],
        'web_profiler' => [
            'toolbar' => true,
        ],
    ],
    'when@test' => [
        'framework' => [
            'profiler' => true,
        ],
    ],
]);
