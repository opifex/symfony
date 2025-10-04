<?php

declare(strict_types=1);

use App\Infrastructure\Messenger\Middleware\RequestTraceMiddleware;
use App\Infrastructure\Messenger\Middleware\MessageValidationMiddleware;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\RemoteEvent\Messenger\ConsumeRemoteEventMessage;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->messenger()->defaultBus(value: 'default.bus');
    $framework->messenger()->failureTransport(value: 'failed');

    $framework->messenger()->bus(name: 'default.bus')
        ->middleware(value: RequestTraceMiddleware::class)
        ->middleware(value: MessageValidationMiddleware::class)
        ->middleware(value: 'doctrine_close_connection')
        ->middleware(value: 'doctrine_ping_connection')
        ->middleware(value: 'doctrine_transaction');

    $framework->messenger()->transport(name: 'notifier_emails')
        ->dsn(value: '%env(MESSENGER_TRANSPORT_DSN)%')
        ->options([
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
        ])
        ->retryStrategy()->delay(value: 60000)->maxRetries(value: 3)->multiplier(value: 1);

    $framework->messenger()->transport(name: 'webhook_events')
        ->dsn(value: '%env(MESSENGER_TRANSPORT_DSN)%')
        ->options([
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
        ])
        ->retryStrategy()->delay(value: 60000)->maxRetries(value: 3)->multiplier(value: 1);

    $framework->messenger()->transport(name: 'failed')
        ->dsn(value: 'doctrine://default?queue_name=failed')
        ->options(['table_name' => 'messenger']);

    $framework->messenger()->routing(message_class: SendEmailMessage::class)
        ->senders(['notifier_emails']);

    $framework->messenger()->routing(message_class: ConsumeRemoteEventMessage::class)
        ->senders(['webhook_events']);
};
