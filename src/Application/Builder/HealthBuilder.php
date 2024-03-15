<?php

declare(strict_types=1);

namespace App\Application\Builder;

use App\Domain\Entity\Health;
use App\Domain\Entity\HealthStatus;

final class HealthBuilder
{
    public static function getAliveHealth(): Health
    {
        return new Health(status: HealthStatus::Ok);
    }
}
