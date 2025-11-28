<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

// todo: validate configuration
return App::config([
    'when@dev' => [
        'monolog' => [
            'handlers' => [
                [
                    'name' => 'nested',
                    'type' => 'stream',
                    'level' => 'debug',
                    'path' => 'php://stdout',
                    'formatter' => 'monolog.formatter.json',
                    'channels' => [
                        '!console',
                        '!deprecation',
                        '!doctrine',
                        '!event',
                        '!http_client',
                        '!php',
                    ],
                ],
            ],
        ],
    ],
    'when@test' => [
        'monolog' => [
            'handlers' => [
                [
                    'name' => 'nested',
                    'type' => 'stream',
                    'level' => 'debug',
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                ],
                [
                    'name' => 'main',
                    'type' => 'fingers_crossed',
                    'action_level' => 'error',
                    'handler' => 'nested',
                    // 'excluded_http_codes' => [
                    //     ['code' => 404],
                    //     ['code' => 405],
                    // ],
                    // 'excluded_http_codes' => [404, 405],
                    // 'channels' => [
                    //     '!event',
                    // ]
                ],
            ],
        ],
    ],
    'when@prod' => [
        'monolog' => [
            'handlers' => [
                [
                    'name' => 'nested',
                    'type' => 'stream',
                    'level' => 'info',
                    'path' => 'php://stdout',
                    'formatter' => 'monolog.formatter.json',
                    'channels' => [
                        '!deprecation',
                        '!doctrine',
                        '!event',
                        '!php',
                        '!security',
                    ],
                ],
            ],
        ],
    ],
]);
