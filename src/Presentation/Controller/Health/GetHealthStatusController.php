<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Health;

use App\Application\MessageHandler\GetHealthStatus\GetHealthStatusRequest;
use App\Domain\Model\HealthStatus;
use App\Domain\Model\HttpSpecification;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class GetHealthStatusController extends AbstractController
{
    #[OA\Get(
        summary: 'Get health status',
        tags: ['Health'],
        responses: [
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: HttpSpecification::STATUS_BAD_REQUEST,
            ),
            new OA\Response(
                response: Response::HTTP_OK,
                description: HttpSpecification::STATUS_OK,
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
            ),
        ],
    )]
    #[Route(
        path: '/health',
        name: 'app_get_health_status',
        methods: Request::METHOD_GET,
    )]
    public function __invoke(#[ValueResolver('payload')] GetHealthStatusRequest $request): Response
    {
        return $this->getHandledResult($request);
    }
}
