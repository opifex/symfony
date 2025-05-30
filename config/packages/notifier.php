<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'framework', config: [
        'notifier' => [
            // 'chatter_transports' => [
            //     'slack' => '%env(SLACK_DSN)%',
            //     'telegram' => '%env(TELEGRAM_DSN)%',
            // ],
            // 'texter_transports' => [
            //     'twilio' => '%env(TWILIO_DSN)%',
            //     'nexmo' => '%env(NEXMO_DSN)%',
            // ],
            'channel_policy' => [
                // chat/slack, chat/telegram, sms/twilio, sms/nexmo
                'urgent' => ['email'],
                'high' => ['email'],
                'medium' => ['email'],
                'low' => ['email'],
            ],
            'admin_recipients' => [
                ['email' => 'admin@example.com'],
            ],
        ],
    ]);
};
