<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetHealthStatus;

use App\Application\Service\HealthEntityBuilder;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetHealthStatusHandler
{
    public function __invoke(GetHealthStatusRequest $message): GetHealthStatusResponse
    {
        $health = HealthEntityBuilder::getAliveHealth();

        return GetHealthStatusResponse::create($health);
    }
}
