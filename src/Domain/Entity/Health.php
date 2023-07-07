<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contract\HealthInterface;

class Health implements HealthInterface
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
