<?php

declare(strict_types=1);

use App\Infrastructure\Adapter\PayPal\Webhook\PayPalRequestParser;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'framework' => [
        'webhook' => [
            'enabled' => true,
            'routing' => [
                [
                    'type' => 'paypal',
                    'service' => PayPalRequestParser::class,
                    'secret' => '%env(PAYPAL_WEBHOOK_TOKEN)%',
                ],
            ],
        ],
    ],
]);
