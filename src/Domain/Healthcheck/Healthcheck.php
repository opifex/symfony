<?php

declare(strict_types=1);

namespace App\Domain\Healthcheck;

final readonly class Healthcheck
{
    private function __construct(
        public HealthStatus $status,
    ) {
    }

    public static function ok(): self
    {
        return new self(status: HealthStatus::Ok);
    }
}
