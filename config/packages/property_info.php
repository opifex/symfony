<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

return function (FrameworkConfig $framework): void {
    $framework->propertyInfo()->withConstructorExtractor(value: true);
};
