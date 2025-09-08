<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

return static function (ContainerConfigurator $configurator, FrameworkConfig $framework): void {
    $framework->secret(value: '%env(APP_SECRET)%');
    $framework->trustedProxies(value: '%env(TRUSTED_PROXIES)%');
    $framework->handleAllThrowables(value: true);
    $framework->httpMethodOverride(value: false);
    $framework->setLocaleFromAcceptLanguage(value: true);
    $framework->setContentLanguageFromLocale(value: true);
    $framework->serializer()->nameConverter(value: 'serializer.name_converter.camel_case_to_snake_case');
    $framework->phpErrors()->log();

    if ($configurator->env() === 'test') {
        $framework->test(value: true);
        $framework->session()->storageFactoryId(value: 'session.storage.factory.mock_file');
    }
};
