<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $configurator): void {
    $configurator->import(resource: '../../src/Presentation/Controller', type: 'attribute')
        ->prefix(prefix: '/api', trailingSlashOnRoot: false);
};
