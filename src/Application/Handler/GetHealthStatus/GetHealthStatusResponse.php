<?php

declare(strict_types=1);

namespace App\Application\Handler\GetHealthStatus;

use App\Domain\Entity\Health;
use App\Domain\Entity\HealthStatus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: Response::HTTP_OK)]
final class GetHealthStatusResponse
{
    public readonly HealthStatus $status;

    public function __construct(Health $health)
    {
        $this->status = $health->getStatus();
    }
}
