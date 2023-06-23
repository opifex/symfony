<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Contract\HealthFactoryInterface;
use App\Domain\Entity\Health;
use App\Domain\Entity\HealthStatus;

final class HealthFactory implements HealthFactoryInterface
{
    public function createAliveHealth(): Health
    {
        return new Health(status: HealthStatus::OK);
    }
}
