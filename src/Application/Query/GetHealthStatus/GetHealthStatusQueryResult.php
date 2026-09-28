<?php

declare(strict_types=1);

namespace App\Application\Query\GetHealthStatus;

use App\Domain\Foundation\Enum\HealthStatus;
use JsonSerializable;
use Override;

final readonly class GetHealthStatusQueryResult implements JsonSerializable
{
    private function __construct(
        private mixed $payload = null,
    ) {
    }

    public static function success(HealthStatus $healthStatus): self
    {
        return new self(
            payload: [
                'status' => $healthStatus->toString(),
            ],
        );
    }

    #[Override]
    public function jsonSerialize(): mixed
    {
        return $this->payload;
    }
}
