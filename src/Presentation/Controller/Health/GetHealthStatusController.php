<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Health;

use App\Application\Query\GetHealthStatus\GetHealthStatusQuery;
use App\Application\Query\GetHealthStatus\GetHealthStatusQueryResult;
use App\Domain\Healthcheck\HealthStatus;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class GetHealthStatusController extends AbstractController
{
    #[OA\Get(summary: 'Get health status')]
    #[OA\Tag(name: 'Health')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'OK',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'status',
                    type: 'string',
                    enum: HealthStatus::class,
                    example: HealthStatus::Ok,
                ),
            ],
            type: 'object',
        ),
    )]
    #[OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request')]
    #[Route(path: '/health', name: 'app_get_health_status', methods: Request::METHOD_GET)]
    public function __invoke(#[ValueResolver('payload')] GetHealthStatusQuery $query): Response
    {
        /** @var GetHealthStatusQueryResult $handledResult */
        $handledResult = $this->queryMessageBus->ask($query);

        return new JsonResponse(
            data: $handledResult->getPayload(),
            status: Response::HTTP_OK,
        );
    }
}
