<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    if ($configurator->env() === 'dev') {
        $configurator->extension(namespace: 'monolog', config: [
            'handlers' => [
                'nested' => [
                    'type' => 'stream',
                    'level' => 'debug',
                    'path' => 'php://stdout',
                    'channels' => ['!deprecation', '!doctrine', '!event', '!http_client', '!php'],
                    'formatter' => 'monolog.formatter.json',
                ],
            ],
        ]);
    }

    if ($configurator->env() === 'test') {
        $configurator->extension(namespace: 'monolog', config: [
            'handlers' => [
                'main' => [
                    'type' => 'fingers_crossed',
                    'action_level' => 'error',
                    'handler' => 'nested',
                    'excluded_http_codes' => [404, 405],
                    'channels' => ['!event'],
                ],
                'nested' => [
                    'type' => 'stream',
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                    'level' => 'debug',
                ],
            ],
        ]);
    }

    if ($configurator->env() === 'prod') {
        $configurator->extension(namespace: 'monolog', config: [
            'handlers' => [
                'nested' => [
                    'type' => 'stream',
                    'level' => 'info',
                    'path' => 'php://stdout',
                    'channels' => ['!deprecation', '!doctrine', '!event', '!php', '!security'],
                    'formatter' => 'monolog.formatter.json',
                ],
            ],
        ]);
    }
};
