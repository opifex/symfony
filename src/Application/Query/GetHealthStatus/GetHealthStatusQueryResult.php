<?php

declare(strict_types=1);

namespace App\Application\Query\GetHealthStatus;

use App\Domain\Foundation\AbstractHandlerResult;
use App\Domain\Healthcheck\Healthcheck;

final class GetHealthStatusQueryResult extends AbstractHandlerResult
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
