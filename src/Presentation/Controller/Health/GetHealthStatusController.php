<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Health;

use App\Application\Attribute\MapMessage;
use App\Application\MessageHandler\GetHealthStatus\GetHealthStatusRequest;
use App\Application\MessageHandler\GetHealthStatus\GetHealthStatusResponse;
use App\Domain\Entity\HealthStatus;
use App\Domain\Entity\HttpSpecification;
use App\Presentation\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[AsController]
final class GetHealthStatusController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[OA\Get(
        summary: 'Get health status',
        tags: ['Health'],
        responses: [
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: HttpSpecification::STATUS_BAD_REQUEST),
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
    #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
    public function __invoke(#[MapMessage] GetHealthStatusRequest $message): Response
    {
        /** @var GetHealthStatusResponse $handledResult */
        $handledResult = $this->handle($message);

        return new JsonResponse(
            data: $this->normalizer->normalize($handledResult),
        );
    }
}
