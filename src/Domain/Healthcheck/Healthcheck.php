<?php

declare(strict_types=1);

namespace App\Domain\Healthcheck;

class Healthcheck
{
    private function __construct(
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
