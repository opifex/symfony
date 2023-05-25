<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Account;

use App\Domain\Entity\AccountRole;
use App\Domain\Entity\AccountStatus;
use App\Domain\Message\GetAccountsByCriteriaQuery;
use App\Domain\Response\GetAccountsByCriteriaItem;
use App\Domain\Response\GetAccountsByCriteriaResponse;
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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[AsController]
final class GetAccountsByCriteriaController extends AbstractController
{
    #[OA\Get(
        summary: 'Get accounts by criteria',
        security: [['bearer' => []]],
        tags: ['Account'],
        parameters: [
            new OA\Parameter(
                name: 'criteria[email]',
                description: 'Search by email field',
                in: 'query',
            ),
            new OA\Parameter(
                name: 'criteria[status]',
                description: 'Search by status field',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: AccountStatus::LIST),
            ),
            new OA\Parameter(
                name: 'sort[created_at]',
                description: 'Sorting field',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']),
            ),
        ],
        responses: [
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Bad Request'),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Forbidden'),
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                headers: [
                    new OA\Header(
                        header: 'X-Total-Count',
                        description: 'Total count of items without limit',
                        schema: new OA\Schema(type: 'int', example: '10'),
                    ),
                ],
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(
                            type: GetAccountsByCriteriaItem::class,
                            groups: [GetAccountsByCriteriaResponse::GROUP_VIEW],
                        ),
                    ),
                ),
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ],
    )]
    #[Route(
        path: '/api/account',
        name: __CLASS__,
        methods: Request::METHOD_GET,
        format: 'json',
    )]
    #[IsGranted(AccountRole::ROLE_ADMIN, message: 'Not privileged to request the resource.')]
    public function __invoke(GetAccountsByCriteriaQuery $message): Envelope
    {
        return $this->queryBus->dispatch($message)->with(
            new SerializerStamp([
                AbstractNormalizer::GROUPS => [
                    GetAccountsByCriteriaResponse::GROUP_VIEW,
                ],
            ]),
        );
    }
}
