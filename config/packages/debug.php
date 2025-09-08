<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DebugConfig;

return #[When(env: 'dev')] static function (ContainerConfigurator $configurator, DebugConfig $debug): void {
    if ($configurator->env() === 'dev') {
        $debug->dumpDestination(value: 'tcp://%env(VAR_DUMPER_SERVER)%');
    }
};
