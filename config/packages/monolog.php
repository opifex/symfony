<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\MonologConfig;

return static function (ContainerConfigurator $configurator, MonologConfig $monolog): void {
    if ($configurator->env() === 'dev') {
        $nestedHandler = $monolog->handler(name: 'nested')
            ->type(value: 'stream')
            ->level(value: 'debug')
            ->path(value: 'php://stdout')
            ->formatter(value: 'monolog.formatter.json');
        $nestedHandler->channels()->elements([
            '!console',
            '!deprecation',
            '!doctrine',
            '!event',
            '!http_client',
            '!php',
        ]);
    }

    if ($configurator->env() === 'test') {
        $monolog->handler('nested')
            ->type(value: 'stream')
            ->level(value: 'debug')
            ->path(value: '%kernel.logs_dir%/%kernel.environment%.log');

        $mainHandler = $monolog->handler(name: 'main')
            ->type(value: 'fingers_crossed')
            ->actionLevel(value: 'error')
            ->handler('nested');
        $mainHandler->excludedHttpCode()->code(value: 404);
        $mainHandler->excludedHttpCode()->code(value: 405);
        $mainHandler->channels()->elements([
            '!event',
        ]);
    }

    if ($configurator->env() === 'prod') {
        $nestedHandler = $monolog->handler(name: 'nested')
            ->type(value: 'stream')
            ->level(value: 'info')
            ->path(value: 'php://stdout')
            ->formatter(value: 'monolog.formatter.json');
        $nestedHandler->channels()->elements([
            '!deprecation',
            '!doctrine',
            '!event',
            '!php',
            '!security',
        ]);
    }
};
