<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

return static function (ContainerConfigurator $configurator, FrameworkConfig $framework): void {
    $framework->router()->defaultUri(value: '%env(resolve:APP_URL)%');
    $framework->router()->utf8(value: true);

    if ($configurator->env() === 'prod') {
        $framework->router()->strictRequirements(value: null);
    }
};
