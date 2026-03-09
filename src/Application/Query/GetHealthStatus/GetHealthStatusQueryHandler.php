<?php

declare(strict_types=1);

namespace App\Application\Query\GetHealthStatus;

use App\Domain\Healthcheck\Healthcheck;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetHealthStatusQueryHandler
{
    public function __invoke(GetHealthStatusQuery $query): GetHealthStatusQueryResult
    {
        $healthCheck = Healthcheck::ok();

        return GetHealthStatusQueryResult::success($healthCheck);
    }
}
