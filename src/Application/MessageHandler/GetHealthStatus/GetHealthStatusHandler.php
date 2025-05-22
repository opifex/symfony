<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetHealthStatus;

use App\Domain\Model\Health;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetHealthStatusHandler
{
    public function __invoke(GetHealthStatusRequest $message): GetHealthStatusResult
    {
        return GetHealthStatusResult::success(Health::ok());
    }
}
