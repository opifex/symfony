<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->cache()->prefixSeed(value: '%env(APP_NAME)%');
    $framework->cache()->defaultRedisProvider(value: '%env(REDIS_DSN)%');

    $framework->cache()->pool(name: 'cache.doctrine')
        ->defaultLifetime(value: 60)
        ->adapters(['cache.adapter.redis', 'cache.adapter.array']);

    $framework->cache()->pool(name: 'cache.storage')
        ->defaultLifetime(value: 60)
        ->adapters(['cache.adapter.redis', 'cache.adapter.array']);
};
