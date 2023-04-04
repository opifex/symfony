<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Domain\Contract\Entity\EntityInterface;
use App\Domain\Entity\Account\Account;
use App\Domain\Entity\Account\AccountRole;
use App\Domain\Message\Account\GetAccountByIdQuery;
use App\Presentation\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\SerializerStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[AsController]
class GetAccountByIdController extends AbstractController
{
    #[OA\Get(
        summary: 'Get account by identifier',
        security: [['bearer' => []]],
        tags: ['Account'],
        parameters: [new OA\Parameter(name: 'uuid', description: 'Account identifier', in: 'path')],
        responses: [
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request'),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Not Found'),
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(
                    ref: new Model(type: Account::class, groups: [EntityInterface::GROUP_VIEW]),
                ),
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ],
    )]
    #[Route(
        path: '/api/account/{uuid}',
        name: __CLASS__,
        methods: Request::METHOD_GET,
        format: JsonEncoder::FORMAT,
    )]
    #[IsGranted(AccountRole::ROLE_ADMIN, message: 'Not privileged to request the resource.')]
    public function __invoke(GetAccountByIdQuery $message): Envelope
    {
        return $this->queryBus->dispatch($message)->with(
            new SerializerStamp([AbstractNormalizer::GROUPS => [EntityInterface::GROUP_VIEW]]),
        );
    }
}
