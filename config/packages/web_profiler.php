<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;
use Symfony\Config\WebProfilerConfig;

return #[When(env: 'dev')] #[When(env: 'test')] static function (ContainerConfigurator $configurator, FrameworkConfig $framework, WebProfilerConfig $webProfiler): void {
    if ($configurator->env() === 'dev') {
        $framework->profiler()->collect(value: true);
        $webProfiler->toolbar()->enabled(value: true);
    }

    if ($configurator->env() === 'test') {
        $framework->profiler()->enabled(value: true);
    }
};
