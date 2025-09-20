<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $configurator): void {
    $configurator->add(name: 'app_webhook_api', path: '/webhook/{type}')
        ->defaults(['_controller' => 'webhook.controller::handle'])
        ->requirements(['type' => '.+']);
};
