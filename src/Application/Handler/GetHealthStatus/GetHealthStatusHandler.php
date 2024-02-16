<?php

declare(strict_types=1);

namespace App\Application\Handler\GetHealthStatus;

use App\Application\Factory\HealthFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetHealthStatusHandler
{
    public function __invoke(GetHealthStatusQuery $message): GetHealthStatusResponse
    {
        $health = HealthFactory::createAliveHealth();

        return new GetHealthStatusResponse($health);
    }
}
