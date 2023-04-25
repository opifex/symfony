<?php

declare(strict_types=1);

namespace App\Application\Handler\Health;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Health\Health;
use App\Domain\Entity\Health\HealthStatus;
use App\Domain\Message\Health\GetHealthStatusQuery;
use App\Domain\Response\GetHealthStatusResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::QUERY)]
final class GetHealthStatusHandler
{
    public function __invoke(GetHealthStatusQuery $message): GetHealthStatusResponse
    {
        return new GetHealthStatusResponse(new Health(status: HealthStatus::OK));
    }
}
