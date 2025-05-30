<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $configurator): void {
    $configurator->add(name: 'app_swagger_json', path: '/docs/json')
        ->methods(['GET'])
        ->defaults(['_controller' => 'nelmio_api_doc.controller.swagger_json']);

    $configurator->add(name: 'app_swagger_ui', path: '/docs')
        ->methods(['GET'])
        ->defaults(['_controller' => 'nelmio_api_doc.controller.swagger_ui']);
};
