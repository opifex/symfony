<?php

declare(strict_types=1);

namespace App\Domain\Response;

use App\Domain\Entity\Health\Health;
use App\Domain\Entity\Health\HealthStatus;

final class GetHealthStatusResponse
{
    public readonly HealthStatus $status;

    public function __construct(Health $health)
    {
        $this->status = $health->getStatus();
    }
}
