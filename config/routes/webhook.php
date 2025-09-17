<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $configurator): void {
    $configurator->import(resource: '@FrameworkBundle/Resources/config/routing/webhook.php')
        ->namePrefix(namePrefix: 'app')
        ->prefix(prefix: '/webhook');
};
