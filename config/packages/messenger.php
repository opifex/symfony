<?php

declare(strict_types=1);

use App\Infrastructure\Messenger\Middleware\RequestIdMiddleware;
use App\Infrastructure\Messenger\Middleware\ValidationMiddleware;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Config\FrameworkConfig;

return function (FrameworkConfig $framework): void {
    $framework->messenger()->defaultBus(value: 'default.bus');
    $framework->messenger()->failureTransport(value: 'failed');

    $framework->messenger()->bus(name: 'default.bus')
        ->middleware(value: RequestIdMiddleware::class)
        ->middleware(value: ValidationMiddleware::class)
        ->middleware(value: 'doctrine_close_connection')
        ->middleware(value: 'doctrine_ping_connection')
        ->middleware(value: 'doctrine_transaction');

    $framework->messenger()->transport(name: 'notifications_email')
        ->dsn(value: '%env(MESSENGER_TRANSPORT_DSN)%')
        ->options([
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
        ])
        ->retryStrategy()->delay(value: 60000)->maxRetries(value: 3)->multiplier(value: 1);

    $framework->messenger()->transport(name: 'failed')
        ->dsn(value: 'doctrine://default?queue_name=failed')
        ->options(['table_name' => 'messenger']);

    $framework->messenger()->routing(message_class: SendEmailMessage::class)
        ->senders(['notifications_email']);
};
