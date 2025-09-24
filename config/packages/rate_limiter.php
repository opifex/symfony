<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->rateLimiter()->limiter(name: 'authentication')
        ->policy(value: 'fixed_window')
        ->limit(value: 5)
        ->cachePool(value: 'cache.rate_limiter')
        ->interval(value: '1 minute');
};
