<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'framework' => [
        'router' => [
            'enabled' => true,
            'default_uri' => '%env(resolve:APP_URL)%',
            'utf8' => true,
        ],
    ],
    'when@prod' => [
        'framework' => [
            'router' => [
                'enabled' => true,
                'strict_requirements' => null,
            ],
        ],
    ],
]);
