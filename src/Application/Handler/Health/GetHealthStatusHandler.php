<?php

declare(strict_types=1);

namespace App\Application\Handler\Health;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Health\Health;
use App\Domain\Entity\Health\HealthStatus;
use App\Domain\Message\Health\GetHealthStatusQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::QUERY)]
class GetHealthStatusHandler
{
    public function __invoke(GetHealthStatusQuery $message): Health
    {
        return new Health(status: HealthStatus::OK);
    }
}
