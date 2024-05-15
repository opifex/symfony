<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetHealthStatus;

use App\Domain\Entity\Health;
use App\Domain\Entity\HealthStatus;

final class GetHealthStatusResponse
{
    public readonly HealthStatus $status;

    public function __construct(Health $health)
    {
        $this->status = $health->getStatus();
    }
}
