<?php

declare(strict_types=1);

use App\Infrastructure\Adapter\PayPal\Webhook\PayPalRequestParser;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->webhook()->routing(type: 'paypal')
        ->service(value: PayPalRequestParser::class)
        ->secret(value: '%env(PAYPAL_WEBHOOK_TOKEN)%');
};
