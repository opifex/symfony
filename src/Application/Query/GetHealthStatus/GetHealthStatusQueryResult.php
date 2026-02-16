<?php

declare(strict_types=1);

namespace App\Application\Query\GetHealthStatus;

use App\Domain\Foundation\MessageHandlerResult;
use App\Domain\Healthcheck\Healthcheck;

final class GetHealthStatusQueryResult extends MessageHandlerResult
{
    public static function success(Healthcheck $health): self
    {
        return new self(
            payload: [
                'status' => $health->getStatus()->toString(),
            ],
        );
    }
}
