<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Entity\Health;

interface HealthFactoryInterface
{
    public function createAliveHealth(): Health;
}
