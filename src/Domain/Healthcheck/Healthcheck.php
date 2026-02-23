<?php

declare(strict_types=1);

namespace App\Domain\Healthcheck;

final class Healthcheck
{
    private function __construct(
        public readonly HealthStatus $status,
    ) {
    }

    public static function ok(): self
    {
        return new self(status: HealthStatus::Ok);
    }
}
