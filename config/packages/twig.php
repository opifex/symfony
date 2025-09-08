<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\TwigConfig;

return static function (ContainerConfigurator $configurator, TwigConfig $twig): void {
    $twig->defaultPath(value: '%kernel.project_dir%/src/Presentation/Resource/Template');
    $twig->path(paths: '%kernel.project_dir%/src/Presentation/Resource/Template/emails', value: 'emails');
    $twig->path(paths: '%kernel.project_dir%/src/Presentation/Resource/Template/views', value: 'views');

    if ($configurator->env() === 'test') {
        $twig->strictVariables(value: true);
    }
};
