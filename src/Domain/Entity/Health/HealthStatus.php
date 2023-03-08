<?php

declare(strict_types=1);

namespace App\Domain\Entity\Health;

enum HealthStatus: string
{
    case OK = 'OK';
}
