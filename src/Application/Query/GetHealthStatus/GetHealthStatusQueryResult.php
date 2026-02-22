<?php

declare(strict_types=1);

namespace App\Application\Query\GetHealthStatus;

use App\Domain\Healthcheck\Healthcheck;
use JsonSerializable;
use Override;

final class GetHealthStatusQueryResult implements JsonSerializable
{
    private function __construct(
        private readonly mixed $payload = null,
    ) {
    }

    public static function success(Healthcheck $health): self
    {
        return new self(
            payload: [
                'status' => $health->status->toString(),
            ],
        );
    }

    #[Override]
    public function jsonSerialize(): mixed
    {
        return $this->payload;
    }
}
