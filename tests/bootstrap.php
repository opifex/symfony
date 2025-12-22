<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(path: __DIR__) . '/vendor/autoload.php';

if (method_exists(object_or_class: Dotenv::class, method: 'bootEnv')) {
    new Dotenv()->bootEnv(path: dirname(path: __DIR__) . '/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(mask: 0000);
}
