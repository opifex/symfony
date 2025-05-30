<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'framework', config: [
        'default_locale' => 'en-US',
        'translator' => [
            'default_path' => '%kernel.project_dir%/src/Presentation/Resource/Translation',
            'fallbacks' => ['en-US'],
        ],
    ]);
};
