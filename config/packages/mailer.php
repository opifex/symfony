<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->mailer()->dsn(value: '%env(MAILER_DSN)%');
    $framework->mailer()->envelope()->sender(value: '%env(MAILER_SENDER)%');
};
