<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetHealthStatus;

use App\Domain\Healthcheck\Healthcheck;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetHealthStatusHandler
{
    public function __invoke(GetHealthStatusRequest $request): GetHealthStatusResult
    {
        return GetHealthStatusResult::success(Healthcheck::ok());
    }
}
