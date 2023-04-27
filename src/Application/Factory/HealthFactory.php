<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Entity\Health\Health;
use App\Domain\Entity\Health\HealthStatus;

final class HealthFactory
{
    public function createAliveHealth(): Health
    {
        return new Health(status: HealthStatus::OK);
    }
}
