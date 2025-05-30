<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    if ($configurator->env() === 'dev') {
        $configurator->extension(namespace: 'web_profiler', config: [
            'toolbar' => true,
            'intercept_redirects' => false,
        ]);

        $configurator->extension(namespace: 'framework', config: [
            'profiler' => [
                'only_exceptions' => false,
                'collect_serializer_data' => true,
            ],
        ]);
    }

    if ($configurator->env() === 'test') {
        $configurator->extension(namespace: 'web_profiler', config: [
            'toolbar' => false,
            'intercept_redirects' => false,
        ]);

        $configurator->extension(namespace: 'framework', config: [
            'profiler' => [
                'collect' => false,
            ],
        ]);
    }
};
