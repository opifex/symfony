<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contract\HealthInterface;
use Override;

class Health implements HealthInterface
{
    public function __construct(private HealthStatus $status)
    {
    }

    #[Override]
    public function getStatus(): HealthStatus
    {
        return $this->status;
    }
}
