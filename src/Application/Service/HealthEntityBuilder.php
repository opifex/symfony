<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\Health;
use App\Domain\Entity\HealthStatus;

final class HealthEntityBuilder
{
    public static function getAliveHealth(): Health
    {
        return new Health(status: HealthStatus::Ok);
    }
}
