<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'framework', config: [
        'mailer' => [
            'dsn' => '%env(MAILER_DSN)%',
            'envelope' => [
                'sender' => '%env(MAILER_SENDER)%',
            ],
        ],
    ]);
};
