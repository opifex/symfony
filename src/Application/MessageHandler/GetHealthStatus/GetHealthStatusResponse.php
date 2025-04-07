<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetHealthStatus;

use App\Domain\Entity\Health;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Exclude]
final class GetHealthStatusResponse extends JsonResponse
{
    public static function create(Health $health): self
    {
        return new self(
            data: [
                'status' => $health->getStatus(),
            ],
            status: Response::HTTP_OK,
        );
    }
}
