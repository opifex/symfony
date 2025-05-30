<?php

declare(strict_types=1);

use App\Infrastructure\Messenger\Middleware\RequestIdMiddleware;
use App\Infrastructure\Messenger\Middleware\ValidationMiddleware;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

return function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'framework', config: [
        'messenger' => [
            'default_bus' => 'default.bus',
            'failure_transport' => 'failed',
            'buses' => [
                'default.bus' => [
                    'middleware' => [
                        RequestIdMiddleware::class,
                        ValidationMiddleware::class,
                        'doctrine_close_connection',
                        'doctrine_ping_connection',
                        'doctrine_transaction',
                    ],
                ],
            ],
            'transports' => [
                'notifications_email' => [
                    'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                    'options' => [
                        'exchange' => [
                            'default_publish_routing_key' => 'normal',
                            'name' => '%env(APP_NAME)%.notifications_email.direct',
                            'type' => 'direct',
                        ],
                        'queues' => [
                            '%env(APP_NAME)%.notifications_email.direct' => [
                                'binding_keys' => ['normal'],
                            ],
                        ],
                    ],
                    'retry_strategy' => [
                        'delay' => 60000,
                        'max_retries' => 3,
                        'multiplier' => 1,
                    ],
                ],
                'failed' => [
                    'dsn' => 'doctrine://default?queue_name=failed',
                    'options' => [
                        'table_name' => 'messenger',
                    ],
                ],
            ],
            'routing' => [
                SendEmailMessage::class => 'notifications_email',
            ],
        ],
    ]);
};
