<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    if ($configurator->env() === 'dev') {
        $configurator->extension(namespace: 'web_profiler', config: [
            'toolbar' => true,
        ]);
    }

    if ($configurator->env() === 'test') {
        $configurator->extension(namespace: 'web_profiler', config: [
            'toolbar' => false,
        ]);

        $configurator->extension(namespace: 'framework', config: [
            'profiler' => [
                'collect' => false,
            ],
        ]);
    }
};
