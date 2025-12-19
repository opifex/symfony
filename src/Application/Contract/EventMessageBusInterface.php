<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface EventMessageBusInterface
{
    public function publish(object $event): void;
}
