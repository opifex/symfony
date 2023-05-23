<?php

declare(strict_types=1);

namespace App\Application\Handler\Health;

use App\Application\Factory\HealthFactory;
use App\Domain\Message\Health\GetHealthStatusQuery;
use App\Domain\Response\Health\GetHealthStatusResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
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
