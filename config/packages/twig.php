<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\App;

//  * @psalm-type TwigConfig = array{
//  *     form_themes?: list<scalar|null>,
//  *     globals?: array<string, array{ // Default: []
//  *         id?: scalar|null,
//  *         type?: scalar|null,
//  *         value?: mixed,
//  *     }>,
//  *     autoescape_service?: scalar|null, // Default: null
//  *     autoescape_service_method?: scalar|null, // Default: null
//  *     base_template_class?: scalar|null, // Deprecated: The child node "base_template_class" at path "twig.base_template_class" is

// todo: validate configuration
return App::config([
    'twig' => [
        'default_path' => '%kernel.project_dir%/src/Presentation/Resource/Template',
        'paths' => [
            '%kernel.project_dir%/src/Presentation/Resource/Template/emails' => 'emails',
            '%kernel.project_dir%/src/Presentation/Resource/Template/views' => 'views',
        ],
    ],
    'when@test' => [
        'twig' => [
            'strict_variables' => true,
        ],
    ],
]);
