<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

// todo: validate configuration
return App::config([
    'framework' => [
        'notifier' => [
            'enabled' => true,
            'channel_policy' => [
                'urgent' => ['email'],
                'high' => ['email'],
                'medium' => ['email'],
                'low' => ['email'],
            ],
            'admin_recipients' => [
                [
                    'email' => 'admin@example.com',
                ],
            ],
        ],
    ],
]);
