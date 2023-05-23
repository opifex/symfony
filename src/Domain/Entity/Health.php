<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class Health
{
    protected HealthStatus $status;

    public function __construct(HealthStatus $status)
    {
        $this->status = $status;
    }

    public function getStatus(): HealthStatus
    {
        return $this->status;
    }
}
