<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    if ($configurator->env() === 'dev') {
        $configurator->extension(namespace: 'debug', config: [
            'dump_destination' => 'tcp://%env(VAR_DUMPER_SERVER)%',
        ]);
    }
};
