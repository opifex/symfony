<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'framework', config: [
        'router' => [
            'utf8' => true,
            'default_uri' => '%env(resolve:APP_URL)%',
        ],
    ]);

    if ($configurator->env() === 'prod') {
        $configurator->extension(namespace: 'framework', config: [
            'router' => [
                'strict_requirements' => null,
            ],
        ]);
    }
};
