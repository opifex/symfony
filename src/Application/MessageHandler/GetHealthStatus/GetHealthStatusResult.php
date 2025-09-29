<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetHealthStatus;

use App\Domain\Health\Health;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Exclude]
final class GetHealthStatusResult extends JsonResponse
{
    public static function success(Health $health): self
    {
        return new self(
            data: [
                'status' => $health->getStatus()->toString(),
            ],
            status: Response::HTTP_OK,
        );
    }
}
