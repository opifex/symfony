<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $configurator): void {
    if ($configurator->env() === 'dev') {
        $configurator->import(resource: '@WebProfilerBundle/Resources/config/routing/wdt.php')
            ->prefix(prefix: '/_wdt');

        $configurator->import(resource: '@WebProfilerBundle/Resources/config/routing/profiler.php')
            ->prefix(prefix: '/_profiler');
    }
};
