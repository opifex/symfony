<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Entity\HealthStatus;

interface HealthInterface
{
    public function getStatus(): HealthStatus;
}
