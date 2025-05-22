<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class Health
{
    public function __construct(
        public readonly string $status,
    ) {
    }

    public static function ok(): self
    {
        return new self(status: HealthStatus::OK);
    }
}
