<?php

declare(strict_types=1);

namespace App\Domain\Entity;

enum HealthStatus: string
{
    case OK = 'OK';
}
