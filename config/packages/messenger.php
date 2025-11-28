<?php

declare(strict_types=1);

use App\Infrastructure\Messenger\Middleware\MessageValidationMiddleware;
use App\Infrastructure\Messenger\Middleware\RequestTraceMiddleware;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\RemoteEvent\Messenger\ConsumeRemoteEventMessage;

return App::config([
    'framework' => [
        'messenger' => [
            'default_bus' => 'default.bus',
            'failure_transport' => 'failed',
            'buses' => [
                [
                    'name' => 'default.bus',
                    'middleware' => [
                        RequestTraceMiddleware::class,
                        MessageValidationMiddleware::class,
                        'doctrine_close_connection',
                        'doctrine_ping_connection',
                        'doctrine_transaction',
                    ],
                ],
            ],
            'transports' => [
                [
                    'name' => 'notifier_emails',
                    'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                    'options' => [
                        'exchange' => [
                            'default_publish_routing_key' => 'normal',
                            'name' => '%env(APP_NAME)%.notifier_emails.direct',
                            'type' => 'direct',
                        ],
                        'queues' => [
                            '%env(APP_NAME)%.notifier_emails.direct' => [
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
                [
                    'name' => 'webhook_events',
                    'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                    'options' => [
                        'exchange' => [
                            'default_publish_routing_key' => 'normal',
                            'name' => '%env(APP_NAME)%.webhook_events.direct',
                            'type' => 'direct',
                        ],
                        'queues' => [
                            '%env(APP_NAME)%.webhook_events.direct' => [
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
                [
                    'name' => 'failed',
                    'dsn' => 'doctrine://default?queue_name=failed',
                    'options' => [
                        'table_name' => 'messenger',
                    ],
                ],
            ],
            'routing' => [
                SendEmailMessage::class => [
                    'senders' => ['notifier_emails'],
                ],
                ConsumeRemoteEventMessage::class => [
                    'senders' => ['webhook_events'],
                ],
            ],
        ],
    ],
]);
