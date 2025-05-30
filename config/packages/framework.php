<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'framework', config: [
        'secret' => '%env(APP_SECRET)%',
        'trusted_proxies' => '%env(TRUSTED_PROXIES)%',
        'handle_all_throwables' => true,
        'http_method_override' => false,
        'set_locale_from_accept_language' => true,
        'set_content_language_from_locale' => true,
        'serializer' => [
            'name_converter' => 'serializer.name_converter.camel_case_to_snake_case',
        ],
        'php_errors' => [
            'log' => true,
        ],
    ]);

    if ($configurator->env() === 'test') {
        $configurator->extension(namespace: 'framework', config: [
            'test' => true,
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
        ]);
    }
};
