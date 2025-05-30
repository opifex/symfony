<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'twig', config: [
        'default_path' => '%kernel.project_dir%/src/Presentation/Resource/Template',
        'paths' => [
            '%kernel.project_dir%/src/Presentation/Resource/Template/emails' => 'emails',
            '%kernel.project_dir%/src/Presentation/Resource/Template/views' => 'views',
        ],
    ]);

    if ($configurator->env() === 'test') {
        $configurator->extension(namespace: 'twig', config: [
            'strict_variables' => true,
        ]);
    }
};
