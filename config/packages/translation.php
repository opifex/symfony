<?php

declare(strict_types=1);

use App\Domain\Localization\LocaleCode;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;

// todo: validate configuration
return App::config([
    'framework' => [
        'default_locale' => LocaleCode::EnUs->value,
        'translator' => [
            'default_path' => '%kernel.project_dir%/src/Presentation/Resource/Translation',
            'fallbacks' => [
                LocaleCode::EnUs->value,
            ],
        ],
    ],
]);
