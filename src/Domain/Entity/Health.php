<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class Health
{
    public function __construct(
        private readonly HealthStatus $status,
    ) {
    }

    public function getStatus(): HealthStatus
    {
        return $this->status;
    }
}
