<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Query\GetHealthStatus;

use App\Domain\Healthcheck\Healthcheck;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetHealthStatusQueryHandler
{
    public function __invoke(GetHealthStatusQuery $request): GetHealthStatusQueryResult
    {
        return GetHealthStatusQueryResult::success(Healthcheck::ok());
    }
}
