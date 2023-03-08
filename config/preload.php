<?php

declare(strict_types=1);

if (file_exists(filename: dirname(path: __DIR__) . '/var/cache/prod/App_KernelProdContainer.preload.php')) {
    require dirname(path: __DIR__) . '/var/cache/prod/App_KernelProdContainer.preload.php';
}
