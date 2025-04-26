<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetHealthStatus;

use App\Domain\Model\Health;
use App\Domain\Model\HealthStatus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetHealthStatusHandler
{
    public function __invoke(GetHealthStatusRequest $message): GetHealthStatusResult
    {
        $health = new Health(status: HealthStatus::OK);

        return GetHealthStatusResult::success($health);
    }
}
