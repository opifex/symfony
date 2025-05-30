<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'framework', config: [
        'validation' => [
            'email_validation_mode' => 'html5',
        ],
    ]);

    if ($configurator->env() === 'test') {
        $configurator->extension(namespace: 'framework', config: [
            'validation' => [
                'not_compromised_password' => false,
            ],
        ]);
    }
};
