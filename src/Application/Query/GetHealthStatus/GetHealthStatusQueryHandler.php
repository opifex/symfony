<?php

declare(strict_types=1);

namespace App\Application\Query\GetHealthStatus;

use App\Domain\Foundation\Enum\HealthStatus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetHealthStatusQueryHandler
{
    public function __invoke(GetHealthStatusQuery $query): GetHealthStatusQueryResult
    {
        $healthStatus = HealthStatus::Ok;

        return GetHealthStatusQueryResult::success($healthStatus);
    }
}
