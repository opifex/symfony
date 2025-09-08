<?php

declare(strict_types=1);

use App\Domain\Model\LocaleCode;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->defaultLocale(LocaleCode::EnUs->value);
    $framework->translator()->defaultPath(value: '%kernel.project_dir%/src/Presentation/Resource/Translation');
    $framework->translator()->fallbacks([LocaleCode::EnUs->value]);
};
