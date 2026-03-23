<?php

declare(strict_types=1);

namespace App\Application\Query\GetHealthStatus;

use App\Domain\Healthcheck\Healthcheck;
use JsonSerializable;
use Override;

final readonly class GetHealthStatusQueryResult implements JsonSerializable
{
    private function __construct(
        private mixed $payload = null,
    ) {
    }

    public static function success(Healthcheck $healthcheck): self
    {
        return new self(
            payload: [
                'status' => $healthcheck->status->toString(),
            ],
        );
    }

    #[Override]
    public function jsonSerialize(): mixed
    {
        return $this->payload;
    }
}
