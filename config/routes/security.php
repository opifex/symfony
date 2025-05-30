<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $configurator): void {
    $configurator->import(resource: 'security.route_loader.logout', type: 'service');
};
