<?php

declare(strict_types=1);

use Symfony\Config\NelmioApiDocConfig;

return function (NelmioApiDocConfig $nelmioApiDoc): void {
    $nelmioApiDoc->areas(name: 'default')->pathPatterns(['^/api']);
    $nelmioApiDoc->documentation('info', ['title' => '%env(APP_NAME)%', 'version' => '%env(APP_VERSION)%']);
    $nelmioApiDoc->documentation('servers', [['url' => '%env(APP_URL)%', 'description' => 'API Gateway']]);
    $nelmioApiDoc->documentation('components', ['securitySchemes' => ['bearer' => ['type' => 'http', 'scheme' => 'bearer']]]);
};
