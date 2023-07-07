<?php

declare(strict_types=1);

namespace App\Application\Handler\GetHealthStatus;

use App\Domain\Contract\HealthInterface;
use App\Domain\Entity\HealthStatus;

final class GetHealthStatusResponse
{
    public readonly HealthStatus $status;

    public function __construct(HealthInterface $health)
    {
        $this->status = $health->getStatus();
    }
}
