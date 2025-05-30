<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'nelmio_api_doc', config: [
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
        'areas' => [
            'path_patterns' => [
                '^/api',
            ],
        ],
    ]);
};
