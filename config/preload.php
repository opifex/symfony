<?php

declare(strict_types=1);

foreach (glob(pattern: dirname(path: __DIR__) . '/var/cache/prod/*.preload.php') ?: [] as $file) {
    require $file;
}
