<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contract\HealthInterface;

final class Health implements HealthInterface
{
    public function __construct(protected HealthStatus $status)
    {
    }

    public function getStatus(): HealthStatus
    {
        return $this->status;
    }
}
