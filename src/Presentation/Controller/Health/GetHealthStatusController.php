<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Health;

use App\Domain\Contract\Entity\EntityInterface;
use App\Domain\Entity\Health\Health;
use App\Domain\Message\Health\GetHealthStatusQuery;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\SerializerStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[AsController]
class GetHealthStatusController extends AbstractController
{
    #[OA\Get(
        summary: 'Get health status',
        tags: ['Health'],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(
                    ref: new Model(type: Health::class, groups: [EntityInterface::GROUP_VIEW]),
                ),
            ),
        ],
    )]
    #[Route(
        path: '/api/health',
        name: __CLASS__,
        methods: [Request::METHOD_GET],
        format: JsonEncoder::FORMAT,
    )]
    public function __invoke(GetHealthStatusQuery $message): Envelope
    {
        return $this->queryBus->dispatch($message)->with(
            new SerializerStamp([AbstractNormalizer::GROUPS => [EntityInterface::GROUP_VIEW]]),
        );
    }
}
