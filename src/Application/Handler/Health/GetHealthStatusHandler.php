<?php

declare(strict_types=1);

namespace App\Application\Handler\Health;

use App\Application\Factory\HealthFactory;
use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Message\Health\GetHealthStatusQuery;
use App\Domain\Response\GetHealthStatusResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::QUERY)]
final class GetHealthStatusHandler
{
    public function __construct(private HealthFactory $healthFactory)
    {
    }

    public function __invoke(GetHealthStatusQuery $message): GetHealthStatusResponse
    {
        return new GetHealthStatusResponse($this->healthFactory->createAliveHealth());
    }
}
