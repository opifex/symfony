<?php

declare(strict_types=1);

use App\Domain\Model\LocaleCode;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'framework', config: [
        'default_locale' => LocaleCode::EnUs->value,
        'translator' => [
            'default_path' => '%kernel.project_dir%/src/Presentation/Resource/Translation',
            'fallbacks' => [LocaleCode::EnUs->value],
        ],
    ]);
};
