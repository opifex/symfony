<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Health;

use App\Application\Attribute\MapMessage;
use App\Application\Handler\GetHealthStatus\GetHealthStatusQuery;
use App\Application\Handler\GetHealthStatus\GetHealthStatusResponse;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

#[AsController]
final class GetHealthStatusController extends AbstractController
{
    #[OA\Get(
        summary: 'Get health status',
        tags: ['Health'],
        responses: [
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request'),
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(ref: new Model(type: GetHealthStatusResponse::class)),
            ),
        ],
    )]
    #[Route(
        path: '/api/health',
        name: __CLASS__,
        methods: Request::METHOD_GET,
        format: JsonEncoder::FORMAT,
    )]
    #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
    public function __invoke(#[MapMessage] GetHealthStatusQuery $message): Envelope
    {
        return $this->queryBus->dispatch($message);
    }
}
