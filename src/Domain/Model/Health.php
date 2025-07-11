<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class Health
{
    public function __construct(
        private readonly HealthStatus $status,
    ) {
    }

    public static function ok(): self
    {
        return new self(status: HealthStatus::Ok);
    }

    public function getStatus(): HealthStatus
    {
        return $this->status;
    }
}
