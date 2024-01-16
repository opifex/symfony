<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(path: __DIR__) . '/vendor/autoload.php';

if (file_exists(filename: dirname(path: __DIR__) . '/config/bootstrap.php')) {
    require dirname(path: __DIR__) . '/config/bootstrap.php';
} elseif (method_exists(object_or_class: Dotenv::class, method: 'bootEnv')) {
    (new Dotenv())->bootEnv(path: dirname(path: __DIR__) . '/.env');
}
