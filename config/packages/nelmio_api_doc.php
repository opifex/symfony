<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'nelmio_api_doc' => [
        'areas' => [
            [
                'name' => 'default',
                'path_patterns' => ['^/api'],
            ],
        ],
        'documentation' => [
            'info' => [
                'title' => '%env(APP_NAME)%',
                'version' => '%env(APP_VERSION)%',
            ],
            'servers' => [
                [
                    'url' => '%env(APP_URL)%',
                    'description' => 'API Gateway',
                ],
            ],
            'components' => [
                'securitySchemes' => [
                    'bearer' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                    ],
                ],
            ],
        ],
    ],
]);
