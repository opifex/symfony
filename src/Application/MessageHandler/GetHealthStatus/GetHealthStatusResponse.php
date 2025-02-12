<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetHealthStatus;

use App\Domain\Entity\Health;
use App\Domain\Entity\HealthStatus;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class GetHealthStatusResponse
{
    public function __construct(
        public readonly HealthStatus $status,
    ) {
    }

    public static function create(Health $health): self
    {
        return new self($health->getStatus());
    }
}
